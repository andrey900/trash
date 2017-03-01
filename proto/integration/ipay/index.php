<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include(GetLangFileName(dirname(__FILE__)."/lang/", "/result.php"));
CModule::IncludeModule("sale");
CModule::IncludeModule("iblock");
CModule::IncludeModule("user");
date_default_timezone_set('Europe/Minsk');

class IPayHandler{
    private $site_name;
    private $name;
    private $surname;
    private $patronymic;
    private $is_test;

    private $_const;
    private $xml;
    private $postXML;
    private $type;
    private $trxID;
    private $orderID;
    private $arOrder;
    private $sign;
    private $shop_sign;
    private $response;
    private $error_response;
    private $amount;
    private $salt;
    private $signature;
    private $user;

//CONSTRUCT
    function __construct(){
        //xml
        $this->xml = simplexml_load_string($_REQUEST["XML"]);
        $this->type = $this->xml->RequestType;
        $this->orderID = $this->xml->PersonalAccount;
        $this->isTransErr = $this->xml->TransactionResult->ErrorText ? true : false;
        $this->trxID = $this->xml->TransactionStart->TransactionId;

        //for ipay
        $this->postXML = $_REQUEST['XML'];
        $this->sign = $_SERVER['HTTP_SERVICEPROVIDER_SIGNATURE'];  // подпись iPay в заголовке запроса
        $this->shop_sign = 'SALT+MD5: ';
        $this->signature = '';
        $this->error_response = false;

        //bitrix
        $this->arOrder = CSaleOrder::GetByID($this->orderID);
        $rsUser = CUser::GetByID($this->arOrder["USER_ID"]);
        $this->user = $rsUser->Fetch();
        $this->amount = (int) $this->arOrder["PRICE"];

        //bitrix sale values
        CSalePaySystemAction::InitParamArrays(false, $this->arOrder["ID"]);
        $this->site_name = CSalePaySystemAction::GetParamValue("site_name");
        $this->name = preg_replace('/[^а-яА-ЯA-Za-z0-9\-]/', '', CSalePaySystemAction::GetParamValue("name"));
        $this->surname = preg_replace('/[^а-яА-ЯA-Za-z0-9\-]/', '', CSalePaySystemAction::GetParamValue("surname"));
        $this->patronymic = preg_replace('/[^а-яА-ЯA-Za-z0-9\-]/', '', CSalePaySystemAction::GetParamValue("patronymic"));
        $this->_const = CSalePaySystemAction::GetParamValue("salt");
        $this->is_test = (CSalePaySystemAction::GetParamValue("is_test") == "Y");

        // Константа для подписи запросов
        $this->salt = addslashes($this->_const);

        //Чтение и расшифровка xml
        $this->HandleRequest();

        //Проверка цифровой подписи
        if(!$this->is_test)
            $this->DieIfHasError();

        //обработка запроса
        $this->Handle();
    }
//CONSTRUCT END



//READING AND HANDLE XML FUNCTIONS
    private function HandleRequest(){

        // Удаляем лишние символы до начала xml-запроса и после xml-запроса
        $this->postXML = preg_replace('/^.*\<\?xml/sim', '<?xml', $this->postXML);
        $this->postXML = preg_replace('/\<\/ServiceProvider_Request\>.*/sim', '</ServiceProvider_Request>', $_REQUEST['XML']);

        // Избавляемся от экранирования
        $this->postXML = stripslashes($this->postXML);

        // Получаем подпись от iPay
        if (preg_match('/SALT\+MD5\:\s(.*)/', $this->sign, $matches))
        {
            $this->signature = $matches[1];
        }
    }

    private function DieIfHasError(){
        if (strcasecmp(md5($this->salt.$this->postXML), $this->signature) != 0)
        {
            $this->SetResponse("ERROR_RESPONSE_CP");
            $this->EchoResponse();
            exit(0);
        }
    }

    private function Handle(){
        switch($this->type){
            case "ServiceInfo":
                $this->ServiceInfoHandle();
                break;
            case "TransactionStart":
                $this->TransactionStartHandle();
                break;
            case "TransactionResult":
                $this->TransactionResultHandle();
                break;
            default:
                $this->SetResponse("ERROR");
                break;
        }
    }

    public function EchoResponse(){
        $this->response = trim($this->response);
        if($this->is_test)
            $md5 = md5($this->response);
        else
            $md5 = md5($this->salt . $this->response);
        header("ServiceProvider-Signature: SALT+MD5: $md5");
        header('Content-type: text/xml; charset=windows-1251');
        echo $this->response;
    }
//READING AND HANDLE XML FUNCTIONS END



//BITRIX ORDER CONTROL FUNCTIONS
    private function LockOrder(){
        return CSaleOrder::Lock($this->orderID);
    }

    private function UnLockOrder(){
        return CSaleOrder::UnLock($this->orderID);
    }

    private function PayOrder(){
        return CSaleOrder::PayOrder($this->orderID, "Y");
    }
//BITRIX ORDER CONTROL FUNCTIONS END



//FILLING RESPONSE FUNCTIONS
    private function SetResponse($type){
        $this->response = $this->ReplaceHolded(GetMessage($type));
    }

    private function ReplaceHolded($text){
        $replace = Array(
            "#ORDER_ID#" => $this->orderID,
            "#SITE_NAME#" => $this->site_name,
            "#NAME#" => $this->name,
            "#SURNAME#" => $this->surname,
            "#PATRONYMIC#" => $this->patronymic,
            "#PRICE#" => $this->amount,
            "#TRX_ID#" => $this->trxID
            );
        foreach($replace as $templ => $val){
            $text = str_replace($templ, $val, $text);
        }
        return $text;
    }
//FILLING RESPONSE FUNCTIONS END



//OTHER FUNCTIONS
    private function CheckTransactionResult(){
        return $this->isTransErr;
    }

    public static function GetTrimIconvStr($str){
        if($new_str = iconv("UTF-8", "windows-1251", $str))
            $str = $new_str;
        return trim($str);
    }

    private function DumpInLog($ar){
        ob_start();
        print_r($ar);
        echo "\n";
        $dump = ob_get_clean();
        file_put_contents("ipay_log.txt", $dump, FILE_APPEND);
    }
//OTHER FUNCTIONS END



//SERVICE INFO HANDLE
    private function ServiceInfoHandle(){

        if ( $this->arOrder["ID"] > 0 ) //проверяем, существует ли такой заказ
        {

            if ( $this->arOrder["PAYED"] == 'Y' ) //проверяем, оплачен ли заказ
            {
                $this->SetResponse("ORDER_ALREADY_PAID");
            } else {

                if ( $this->arOrder["CANCELED"] == 'Y' ) //проверяем, отменён ли заказ
                {
                    $this->SetResponse("ORDER_CANCELED");
                } else {
                    CSalePaySystemAction::InitParamArrays(false, $this->arOrder["ID"]);
                    $this->SetResponse("ORDER_INFO");
                }
            }

        } else {
            $this->SetResponse("ORDER_OUTSET");
        }
    }
//SERVICE INFO HANDLE END



//TRANSACTION HANDLE
    private function TransactionStartHandle(){
        if ($this->arOrder["ID"] > 0) {
            if ($this->arOrder["PAYED"] == 'Y') {
                $this->SetResponse("ORDER_ALREADY_PAID");
            } else {
                if ($this->arOrder["DATE_LOCK"]) {
                    //проиходит оплата заказа
                    $this->SetResponse("ORDER_PAYABLE");
                } else {
                    // если предыдущие проверки прошли, обновляем базу и блокируем заказ, ставим `blocked` = 1
                    if($this->LockOrder()){
                        $this->SetResponse("ORDER_CONFIRMATION");
                    } else {
                        $this->SetResponse("ORDER_BLOCKING_ERROR");
                    }
                }
            }
        } else {
            $this->SetResponse("ORDER_OUTSET");
        }
    }
//TRANSACTION HANDLE END



//TRANSACTION RESULT HANDLE
    private function TransactionResultHandle(){
        if (!$this->CheckTransactionResult()) // проверяем была ли ошибка
        {
            $this->UnLockOrder();
            $this->PayOrder();
            $this->SetResponse("ORDER_SUCCESS");
        } else {
            // если была ошибка оплаты, снимаем блокировку с заказа
            $this->UnLockOrder();
            $this->SetResponse("ORDER_CANCEL");
        }
    }
//TRANSACTION RESULT HANDLE END

}
//end class

$ipay = new IPayHandler();
$ipay->EchoResponse();
?>
