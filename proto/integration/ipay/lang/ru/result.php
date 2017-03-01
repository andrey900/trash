<?php
global $MESS;

$MESS["ERROR"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>����������� ������</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ERROR_RESPONSE_CP"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>������ �������� �������� �������</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ORDER_OUTSET"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>����� ����� #ORDER_ID# �� ����������. ������� ������ ������ �� ����� #SITE_NAME#</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ORDER_ALREADY_PAID"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <Error>
            <ErrorLine>����� ����� #ORDER_ID# ��� �������</ErrorLine>
        </Error>
    </ServiceProvider_Response>";

$MESS["ORDER_CANCELED"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <Error>
            <ErrorLine>����� ����� #ORDER_ID# ������.</ErrorLine>
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
                <InfoLine>������ ������ # #ORDER_ID#</InfoLine>
            </Info>
        </ServiceInfo>
    </ServiceProvider_Response>";

$MESS["ORDER_PAYABLE"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <Error>
            <ErrorLine>����� ����� #ORDER_ID# ��������� � �������� ������.</ErrorLine>
        </Error>
    </ServiceProvider_Response>";

$MESS["ORDER_CONFIRMATION"] = "
<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <TransactionStart>
            <ServiceProvider_TrxId>#TRX_ID#</ServiceProvider_TrxId>
            <Info xml:space='preserve'>
                <InfoLine>����� ������: #ORDER_ID#</InfoLine>
                <InfoLine>�����: #PRICE#</InfoLine>
            </Info>
        </TransactionStart>
    </ServiceProvider_Response>";

$MESS["ORDER_BLOCKING_ERROR"] = "
<ServiceProvider_Response>
    <Error>
        <ErrorLine>����� ����� #ORDER_ID#; ��������� ������ ����������.</ErrorLine>
    </Error>
</ServiceProvider_Response>";

$MESS["ORDER_SUCCESS"] = "
<?xml version='1.0' encoding='windows-1251' ?>
<ServiceProvider_Response>
    <TransactionResult>
        <Info xml:space='preserve'>
            <InfoLine>������� �� �������!</InfoLine>
        </Info>
    </TransactionResult>
</ServiceProvider_Response>";

$MESS["ORDER_CANCEL"] = "
'<?xml version='1.0' encoding='windows-1251' ?>
    <ServiceProvider_Response>
        <TransactionResult>
            <Info xml:space='preserve'>
                <InfoLine>�������� ��������</InfoLine>
            </Info>
        </TransactionResult>
    </ServiceProvider_Response>";