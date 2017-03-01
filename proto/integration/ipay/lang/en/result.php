<?php
global $MESS;

$MESS["ERROR"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>Unknown error</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ERROR_RESPONSE_CP"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>Error verifying signature</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ORDER_OUTSET"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>Order # #ORDER_ID# does not exist. Start re-payment site #SITE_NAME#</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ORDER_ALREADY_PAID"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <Error>
            <ErrorLine>Order # #ORDER_ID# already paid</ErrorLine>
        </Error>
    </ServiceProvider_Response>";

$MESS["ORDER_CANCELED"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <Error>
            <ErrorLine>Order # #ORDER_ID# canceled.</ErrorLine>
        </Error>
    </ServiceProvider_Response>";

$MESS["ORDER_INFO"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <ServiceInfo>
            <Amount>
                <Debt>#PRICE#</Debt>
            </Amount>
            <Name>
                <Surname>#SURNAME#</Surname>
                <FirstName>#NAME#</FirstName>
                <Patronymic>#PATRONYMIC#</Patronymic>
            </Name>
            <Info xml:space='preserve'>
                <InfoLine>#SITE_NAME#</InfoLine>
                <InfoLine>Payment order # #ORDER_ID#</InfoLine>
            </Info>
        </ServiceInfo>
    </ServiceProvider_Response>";

$MESS["ORDER_PAYABLE"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <Error>
            <ErrorLine>Order # #ORDER_ID# is in the payment process.</ErrorLine>
        </Error>
    </ServiceProvider_Response>";

$MESS["ORDER_CONFIRMATION"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <TransactionStart>
            <ServiceProvider_TrxId>#TRX_ID#</ServiceProvider_TrxId>
            <Info xml:space='preserve'>
                <InfoLine>Order ID: #ORDER_ID#</InfoLine>
                <InfoLine>Amount: #PRICE#</InfoLine>
            </Info>
        </TransactionStart>
    </ServiceProvider_Response>";

$MESS["ORDER_BLOCKING_ERROR"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>Order # #ORDER_ID#; There was an error lock.</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ORDER_SUCCESS"] = "
<?xml version='1.0' encoding='windows-1251' ?>
<ServiceProvider_Response>
    <TransactionResult>
        <Info xml:space='preserve'>
            <InfoLine>Thank you for your purchase!</InfoLine>
        </Info>
    </TransactionResult>
</ServiceProvider_Response>";

$MESS["ORDER_CANCEL"] = "
'<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <TransactionResult>
            <Info xml:space='preserve'>
                <InfoLine>Transaction canceled</InfoLine>
            </Info>
        </TransactionResult>
    </ServiceProvider_Response>";