#include "readcards.h"
#include "../httpapi/httprequest.h"
#include "../../incIDCardCn/ExportFunc.h"
#include <QJsonObject>
#include <QJsonDocument>
#include <QJsonParseError>
#include <QDebug>

extern HttpRequest request;
ReadIDCard::ReadIDCard(QObject* parent) :QObject(parent)
{
	m_iPort = -1;
	m_readIDCardClock = new QTimer(this);
	m_readIDCardClock->setInterval(100);

	FindReadIDCardDevice();
	connect(m_readIDCardClock, SIGNAL(timeout()), this, SLOT(IDCardClockTimeout()));
	connect(this, SIGNAL(OpenIDCardPortFail()), this, SLOT(FindReadIDCardDevice()));
	//connect(this, SIGNAL(FindDeviceFail()), this, SLOT(ReadIDCardNO()));
	//connect(this, SIGNAL(FindIDcardFail()), this, SLOT(ReadIDCardNO()));
	//connect(this, SIGNAL(SelectIDcardFail()), this, SLOT(ReadIDCardNO()));
	m_readIDCardClock->start();
}

ReadIDCard::~ReadIDCard()
{

}

void ReadIDCard::FindReadIDCardDevice()
{
	QString sMsg, sMsg2;
	unsigned int uiCurrBaud;
	int nRet, nRet2;
	unsigned char nARMVol;

	//CCalcTime tSpan;
	//tSpan.Clear();
	//tSpan.BegginTime();
	nRet = Syn_FindUSBReader();

	//tSpan.EndTime();

	if (nRet == 0)
	{
		//emit cacheTip(QStringLiteral("请连接身份证阅读器"));
		emit connectDevice(0);
		emit FindDeviceFail();
	}
	else
	{
		m_iPort = nRet;
		qDebug() << m_iPort << endl;
		Sleep(500);
		emit connectDevice(1);
		emit CanReadCard();
	}
}

void ReadIDCard::ReadIDCardNO()
{
	if (!FindIDCard())
		return;
	if (!SelectIDCard())
		return;
	if (!ReadCardNO())
		return;
}

bool ReadIDCard::FindIDCard()
{
	int nRet;
	unsigned char pucIIN[4];
	nRet = Syn_OpenPort(m_iPort);
	if (nRet == 0)
	{
		emit connectDevice(1);
		//CCalcTime tSpan;
		//tSpan.Clear();
		//tSpan.BegginTime();
		nRet = Syn_StartFindIDCard(m_iPort, pucIIN, 0);
		//tSpan.EndTime();
		if (nRet == 0)
		{
			return true;
		}
		else
		{
			//emit cacheTip(QStringLiteral("请放入身份证"));
			emit FindIDcardFail();
			return false;
		}
	}
	else
	{
		emit OpenIDCardPortFail();
		emit connectDevice(0);
		return false;
	}
	if (m_iPort>0)
	{
		Syn_ClosePort(m_iPort);
	}
}

bool ReadIDCard::SelectIDCard()
{
	int nRet;
	unsigned char pucSN[8];
	nRet = Syn_OpenPort(m_iPort);
	if (nRet == 0)
	{
		emit connectDevice(1);
		//CCalcTime tSpan;
		//tSpan.Clear();
		//tSpan.BegginTime();
		nRet = Syn_SelectIDCard(m_iPort, pucSN, 0);
		//tSpan.EndTime();
		if (nRet == 0)
		{
			return true;
		}
		else
		{
			//emit cacheTip(QStringLiteral("请放入身份证"));
			emit SelectIDcardFail();
			return false;
		}
	}
	else
	{
		emit OpenIDCardPortFail();
		emit connectDevice(0);
		return false;
	}
	if (m_iPort>0)
	{
		Syn_ClosePort(m_iPort);
	}
}

bool ReadIDCard::ReadCardNO()
{
	int nRet;
	IDCardData idcardData;
	int iPhototype;
	char szBuffer[256] = { 0 };
	char szPath[_MAX_PATH] = { 0 };
	//Syn_SetPhotoPath(1, szBuffer);	//设置照片路径	iOption 路径选项	0=C:	1=当前路径	2=指定路径
	//cPhotoPath	绝对路径,仅在iOption=2时有效
	iPhototype = 0;
	Syn_SetPhotoType(4); //0 = bmp ,1 = jpg , 2 = base64 , 3 = WLT ,4 = 不生成
	Syn_SetPhotoName(2); // 生成照片文件名 0=tmp 1=姓名 2=身份证号 3=姓名_身份证号 

	Syn_SetSexType(1);	// 0=卡中存储的数据	1=解释之后的数据,男、女、未知
	Syn_SetNationType(1);// 0=卡中存储的数据	1=解释之后的数据 2=解释之后加"族"
	Syn_SetBornType(3);			// 0=YYYYMMDD,1=YYYY年MM月DD日,2=YYYY.MM.DD,3=YYYY-MM-DD,4=YYYY/MM/DD
	Syn_SetUserLifeBType(2);	// 0=YYYYMMDD,1=YYYY年MM月DD日,2=YYYY.MM.DD,3=YYYY-MM-DD,4=YYYY/MM/DD
	Syn_SetUserLifeEType(2, 1);	// 0=YYYYMMDD(不转换),1=YYYY年MM月DD日,2=YYYY.MM.DD,3=YYYY-MM-DD,4=YYYY/MM/DD,
	// 0=长期 不转换,	1=长期转换为 有效期开始+50年
	nRet = Syn_OpenPort(m_iPort);
	if (nRet == 0)
	{
		emit connectDevice(1);
		//CCalcTime tSpan;
		//tSpan.Clear();
		//tSpan.BegginTime();
		nRet = Syn_ReadMsg(m_iPort, 0, &idcardData);
		//tSpan.EndTime();
		if (nRet == 0)
		{
			m_idCardNO = QString::fromLocal8Bit(idcardData.IDCardNo);
			m_stuName = QString::fromLocal8Bit(idcardData.Name);
			emit cacheIDCardNO(m_idCardNO, m_stuName);
			return true;
		}
		else
		{
			emit ReadIDcardFail();
			return false;
		}
	}
	else
	{
		//emit cacheTip(QStringLiteral("读身份证失败"));
		emit OpenIDCardPortFail();
		emit connectDevice(0);
		return false;
	}
	if (m_iPort>0)
	{
		Syn_ClosePort(m_iPort);
	}
}

void ReadIDCard::IDCardClockTimeout()
{
	ReadIDCardNO();
}

void ReadIDCard::StopTime()
{
	m_readIDCardClock->stop();
}

void ReadIDCard::StartTime()
{
	m_readIDCardClock->start();
}

//----------------------------------------------------------
ReadSmartCard::ReadSmartCard(QObject* parent) :QObject(parent)
{
	port = -1;
	dllLoad = NULL;
	isLoadDll = false;
	isHavePort = false;
	initLoadLib();
	FindEhuoyanDevice();
	m_readSmartCardClock = new QTimer(this);
	m_readSmartCardClock->setInterval(100);

	connect(m_readSmartCardClock, SIGNAL(timeout()), this, SLOT(SearchSmartCard()));
	connect(this, SIGNAL(initPort()), this, SLOT(FindEhuoyanDevice()));
	connect(this, SIGNAL(siginitLoadDll()), this, SLOT(initLoadLib()));
	m_readSmartCardClock->start();
}

ReadSmartCard::~ReadSmartCard()
{

}

void ReadSmartCard::initLoadLib()
{
	if (dllLoad)
	{
		delete dllLoad;
	}
	dllLoad = new QLibrary("MasterRD.dll");
	if (dllLoad)
	{
		(FARPROC&)lib_ver = (FARPROC)(dllLoad->resolve("lib_ver"));
		(FARPROC&)des_encrypt = (FARPROC)dllLoad->resolve("des_encrypt");
		(FARPROC&)des_decrypt = (FARPROC)dllLoad->resolve("des_decrypt");
		(FARPROC&)rf_init_com = (FARPROC)dllLoad->resolve("rf_init_com");
		(FARPROC&)rf_init_device_number = (FARPROC)dllLoad->resolve("rf_init_device_number");
		(FARPROC&)rf_get_device_number = (FARPROC)dllLoad->resolve("rf_get_device_number");
		(FARPROC&)rf_get_model = (FARPROC)dllLoad->resolve("rf_get_model");
		(FARPROC&)rf_get_snr = (FARPROC)dllLoad->resolve("rf_get_snr");
		(FARPROC&)rf_beep = (FARPROC)dllLoad->resolve("rf_beep");
		(FARPROC&)rf_init_sam = (FARPROC)dllLoad->resolve("rf_init_sam");
		(FARPROC&)rf_sam_rst = (FARPROC)dllLoad->resolve("rf_sam_rst");
		(FARPROC&)rf_sam_cos = (FARPROC)dllLoad->resolve("rf_sam_cos");
		(FARPROC&)rf_init_type = (FARPROC)dllLoad->resolve("rf_init_type");
		(FARPROC&)rf_antenna_sta = (FARPROC)dllLoad->resolve("rf_antenna_sta");
		(FARPROC&)rf_request = (FARPROC)dllLoad->resolve("rf_request");
		(FARPROC&)rf_anticoll = (FARPROC)dllLoad->resolve("rf_anticoll");
		(FARPROC&)rf_select = (FARPROC)dllLoad->resolve("rf_select");
		(FARPROC&)rf_halt = (FARPROC)dllLoad->resolve("rf_halt");
		(FARPROC&)rf_download_key = (FARPROC)dllLoad->resolve("rf_download_key");
		(FARPROC&)rf_M1_authentication1 = (FARPROC)dllLoad->resolve("rf_M1_authentication1");
		(FARPROC&)rf_M1_authentication2 = (FARPROC)dllLoad->resolve("rf_M1_authentication2");
		(FARPROC&)rf_M1_read = (FARPROC)dllLoad->resolve("rf_M1_read");
		(FARPROC&)rf_M1_write = (FARPROC)dllLoad->resolve("rf_M1_write");
		(FARPROC&)rf_M1_initval = (FARPROC)dllLoad->resolve("rf_M1_initval");
		(FARPROC&)rf_M1_readval = (FARPROC)dllLoad->resolve("rf_M1_readval");
		(FARPROC&)rf_M1_decrement = (FARPROC)dllLoad->resolve("rf_M1_decrement");
		(FARPROC&)rf_M1_increment = (FARPROC)dllLoad->resolve("rf_M1_increment");
		(FARPROC&)rf_M1_restore = (FARPROC)dllLoad->resolve("rf_M1_restore");
		(FARPROC&)rf_M1_transfer = (FARPROC)dllLoad->resolve("rf_M1_transfer");
		(FARPROC&)rf_typea_rst = (FARPROC)dllLoad->resolve("rf_typea_rst");
		(FARPROC&)rf_cos_command = (FARPROC)dllLoad->resolve("rf_cos_command");
		(FARPROC&)rf_atqb = (FARPROC)dllLoad->resolve("rf_atqb");
		(FARPROC&)rf_attrib = (FARPROC)dllLoad->resolve("rf_attrib");
		(FARPROC&)rf_typeb_cos = (FARPROC)dllLoad->resolve("rf_typeb_cos");
		(FARPROC&)rf_hltb = (FARPROC)dllLoad->resolve("rf_hltb");
		(FARPROC&)rf_at020_check = (FARPROC)dllLoad->resolve("rf_at020_check");
		(FARPROC&)rf_at020_read = (FARPROC)dllLoad->resolve("rf_at020_read");
		(FARPROC&)rf_at020_write = (FARPROC)dllLoad->resolve("rf_at020_write");
		(FARPROC&)rf_at020_lock = (FARPROC)dllLoad->resolve("rf_at020_lock");
		(FARPROC&)rf_at020_count = (FARPROC)dllLoad->resolve("rf_at020_count");
		(FARPROC&)rf_at020_deselect = (FARPROC)dllLoad->resolve("rf_at020_deselect");
		(FARPROC&)rf_light = (FARPROC)dllLoad->resolve("rf_light");
		(FARPROC&)rf_ClosePort = (FARPROC)dllLoad->resolve("rf_ClosePort");
		(FARPROC&)rf_GetErrorMessage = (FARPROC)dllLoad->resolve("rf_GetErrorMessage");



		if (NULL == lib_ver ||
			NULL == des_encrypt ||
			NULL == des_decrypt ||
			NULL == rf_init_com ||
			NULL == rf_init_device_number ||
			NULL == rf_get_device_number ||
			NULL == rf_get_model ||
			NULL == rf_beep ||
			NULL == rf_init_sam ||
			NULL == rf_sam_rst ||
			NULL == rf_sam_cos ||
			NULL == rf_init_type ||
			NULL == rf_antenna_sta ||
			NULL == rf_request ||
			NULL == rf_anticoll ||
			NULL == rf_select ||
			NULL == rf_halt ||
			NULL == rf_download_key ||
			NULL == rf_M1_authentication1 ||
			NULL == rf_M1_authentication2 ||
			NULL == rf_M1_read ||
			NULL == rf_M1_write ||
			NULL == rf_M1_initval ||
			NULL == rf_M1_readval ||
			NULL == rf_M1_decrement ||
			NULL == rf_M1_increment ||
			NULL == rf_M1_restore ||
			NULL == rf_M1_transfer ||
			NULL == rf_typea_rst ||
			NULL == rf_cos_command ||
			NULL == rf_atqb ||
			NULL == rf_attrib ||
			NULL == rf_typeb_cos ||
			NULL == rf_hltb ||
			NULL == rf_at020_check ||
			NULL == rf_at020_read ||
			NULL == rf_at020_write ||
			NULL == rf_at020_lock ||
			NULL == rf_at020_count ||
			NULL == rf_at020_deselect ||
			NULL == rf_light ||
			NULL == rf_ClosePort ||
			NULL == rf_GetErrorMessage)
		{
			isLoadDll = false;
			//emit cacheTip(QStringLiteral("初始化动态库失败"));
			return;
		}
	}
	else
	{
		isLoadDll = false;
		//emit cacheTip(QStringLiteral("初始化动态库失败"));
		return;
	}
	isLoadDll = true;
}

void ReadSmartCard::FindEhuoyanDevice()
{
	for (int i = 1; i < 10; i++)
	{
		int state = rf_init_com(i, 19200);
		if (state != LIB_SUCCESS)
		{
			port = -1;
			rf_ClosePort();
		}
		else
		{
			port = i;
			break;
		}
	}
}

void ReadSmartCard::SearchSmartCard()
{
	if (!isLoadDll)
	{
		emit siginitLoadDll();
		return;
	}
	int ret = rf_init_com(port, 19200);
	if (ret != LIB_SUCCESS)
	{
		port = -1;
		emit connectDevice(0);
		//emit cacheTip(QStringLiteral("请确保NFC读卡器连接正常"));
		rf_ClosePort();
		emit initPort();
		return;
	}
	emit connectDevice(1);
	unsigned short icdev = 0x0000;
	unsigned char mode = 0x52;
	int status;
	unsigned short TagType;
	unsigned char bcnt = 0x04;//mifare card use 0x04
	unsigned char Snr[MAX_RF_BUFFER];
	unsigned char len;
	unsigned char Size;

	status = rf_request(icdev, mode, &TagType);//search all card
	if (status) {//error
		//emit cacheTip(QStringLiteral("请放入IC卡"));
		data = "";
		return;
	}

	status = rf_anticoll(icdev, bcnt, Snr, &len);//return serial number of card
	if (status || len != 4) { //error
		return;
	}

	status = rf_select(icdev, Snr, len, &Size);//lock ISO14443-3 TYPE_A 
	if (status) {//error
		return;
	}

	QString serial = HexToQString(Snr, len);
	if (data.compare(serial) != 0)
	{
		data = serial;
		status = rf_beep(icdev, bcnt);
	}
	emit cacheSmartID(serial);
}

QString ReadSmartCard::HexToQString(unsigned char* pData, int len)
{
	QString serial;
	for (int k = 0; k < len; k++)
	{
		unsigned short temp = SetHexToAscII(pData[k]);
		serial += temp & 0xff;
		serial += (temp >> 8) & 0xff;
	}
	return serial;
}

BYTE ReadSmartCard::GetHexValue(BYTE ch)
{
	BYTE sz;
	if (ch <= '9' && ch >= '0')
		sz = ch - 0x30;
	if (ch <= 'F' && ch >= 'A')
		sz = ch - 0x37;
	if (ch <= 'f' && ch >= 'a')
		sz = ch - 0x57;

	return sz;
}

unsigned short ReadSmartCard::SetHexToAscII(BYTE szHex)
{
	unsigned short wAscII;
	BYTE loBits = szHex & 0x0f;
	BYTE hiBits = (szHex & 0xf0) >> 4;

	BYTE loByte, hiByte;

	if (loBits <= 9) loByte = loBits + 0x30;
	else loByte = loBits + 0x37;

	if (hiBits <= 9) hiByte = hiBits + 0x30;
	else hiByte = hiBits + 0x37;

	wAscII = MAKEWORD(hiByte, loByte);
	return wAscII;
}

QString ReadSmartCard::ExchangeHexToString(QString strHex)
{
	QString str("");
	int nLen = strHex.length();

	QByteArray pDataArray = strHex.toUtf8();
	char* pData = pDataArray.data();
	for (int i = 0; i < nLen; i++){
		WORD wAscII = SetHexToAscII(pData[i]);
		str += LOBYTE(wAscII);
		str += HIBYTE(wAscII);
	}
	return str;
}

void ReadSmartCard::StopTime()
{
	m_readSmartCardClock->stop();
}

void ReadSmartCard::StartTime()
{
	m_readSmartCardClock->start();
}

//----------------------------------------------------------
ReadAgency::ReadAgency(QObject* parent) :QObject(parent)
{
	isValidIdCardNO = false;
	isValidWatchCardNO = false;
	BindStat = false;
	UnBindStat = false;
	connIDcard = false;
	connSmart = false;
	type = -1;
	m_checkConnClock = new QTimer(this);
	m_checkConnClock->setInterval(100);
	connect(m_checkConnClock, SIGNAL(timeout()), this, SLOT(checkDeviceConn()));
	init();
}

ReadAgency::~ReadAgency()
{

}

void ReadAgency::DealIDCardConnect(int stat)
{
	if (stat == 0)
	{
		connIDcard = false;
		m_checkConnClock->start();
	}
	if (stat == 1)
	{
		connIDcard = true;
	}
}

void ReadAgency::DealSmartConnect(int stat)
{
	if (stat == 0)
	{
		connSmart = false;
		m_checkConnClock->start();
	}
	if (stat == 1)
	{
		connSmart = true;
	}
}

void ReadAgency::checkDeviceConn()
{
	//emit cacheTip(QStringLiteral("请连接身份证阅读器"));
	//emit cacheTip(QStringLiteral("请连接NFC读卡器"));
	if (connIDcard && connSmart)
	{
		emit cacheBindTip(QStringLiteral(""));
		m_checkConnClock->stop();
	}
	else
	{
		if (!connIDcard && !connSmart)
		{
			emit cacheBindTip(QStringLiteral("请连接身份证阅读器、NFC读卡器"));
		}

		if (connIDcard && !connSmart)
		{
			emit cacheBindTip(QStringLiteral("请连接NFC读卡器"));
		}

		if (!connIDcard && connSmart)
		{
			emit cacheBindTip(QStringLiteral("请连接身份证阅读器"));
		}
	}
}

void ReadAgency::init()
{
	connect(&request, SIGNAL(requestData(int, QString, int)), this, SLOT(recevieRequest(int, QString, int)));

	m_readIDCard = new ReadIDCard(this);	
	connect(m_readIDCard, SIGNAL(cacheIDCardNO(QString,QString)), this, SLOT(DealIDcardNO(QString,QString)));
	connect(m_readIDCard, SIGNAL(cacheTip(QString)), this, SIGNAL(cacheIDCardTip(QString)));

	//智能卡
	m_readSmartCard = new ReadSmartCard(this);
	connect(m_readSmartCard, SIGNAL(cacheSmartID(QString)), this, SLOT(DealSmartID(QString)));
	connect(m_readSmartCard, SIGNAL(cacheTip(QString)), this, SIGNAL(cacheWatchReadTip(QString)));

	connect(m_readIDCard, SIGNAL(connectDevice(int)), this, SLOT(DealIDCardConnect(int)));
	connect(m_readSmartCard, SIGNAL(connectDevice(int)), this, SLOT(DealSmartConnect(int)));
}

void ReadAgency::DealIDcardNO(QString data, QString name)
{
	//m_idCardNO = "51068119352986";
	m_idCardNO = data;
	m_stuName = name;
	type = GetStudentInfo;
	//request.getStudentInfoByIDCard("51068119352986", GetStudentInfo);
	request.getStudentInfoByIDCard(m_idCardNO, GetStudentInfo);
}


void ReadAgency::recevieRequest(int stat, QString data, int type)
{
	switch (type)
	{
	case 	GetStudentInfo:
	{
							  recevieIDcardNORequest(stat,data);
							  break;
	}
	case 	GetWatchInfo:
	{
							recevieSmartcardNORequest(stat,data);
							break;
	}
	case 	Bind:
	{
					recevieBindRequest(stat,data);
					break;
	}
	case	UnBind:
	{
					  recevieUnBindRequest(stat,data);
					  break;
	}
	default:
		break;
	}
}

void ReadAgency::recevieIDcardNORequest(int stat, QString data)
{
	qDebug() << "stat="<<stat << endl;
	if (stat != 200)
	{
		emit cacheBindTip(QStringLiteral("请检查网络"));
		//qDebug() << data << endl;
		return;
	}
	qDebug() << data << endl;
	QJsonParseError error;
	QJsonDocument jsonDocument = QJsonDocument::fromJson(data.toUtf8(), &error);

	if (error.error == QJsonParseError::NoError) {
		if (!(jsonDocument.isNull() || jsonDocument.isEmpty()))
		{
			if (jsonDocument.isObject())
			{
				QVariantMap result = jsonDocument.toVariant().toMap();
				int stat = result["code"].toInt();
				switch (stat)
				{
				case 0:
				{
						  //m_stuName = QStringLiteral("顾炎武");
						  isValidIdCardNO = true;
						  QVariantMap dataMap = result["data"].toMap();
						  QString stuNO = dataMap["code"].toString();
						  emit cacheStudentInfo(m_stuName, stuNO, m_idCardNO, "");
						  emit cacheBindTip("");
						  if (isValidWatchCardNO)
						  {
							  StartBind();
						  }
						  /*else
						  {
							  emit cacheSmartCardInfo("", "");
						  }*/
						  break;
				}
				case 1:
				{
						  isValidIdCardNO = false;
						  emit cacheBindTip(QStringLiteral("身份证号") + m_idCardNO + QStringLiteral("已绑定"));
						  emit cacheStudentInfo("", "", "", "");
						  break;
				}
				case 2:
				{
						  isValidIdCardNO = false;
						  emit cacheBindTip(QStringLiteral("此考试未参加当前考试")/* + QStringLiteral("身份证号为") + m_idCardNO*/);
						  emit cacheStudentInfo("", "", "", "");
						  break;
				}
				default:
					break;
				}
			}
			else
			{

			}
		}
	}
	else {
		//qFatal(error.errorString().toUtf8().constData());
		//exit(1);
	}
}

void ReadAgency::DealSmartID(QString id)
{
	//m_smartCard = "999";
	m_smartCard = id;
	type = GetWatchInfo;
	request.getSmartByCardID(m_smartCard, GetWatchInfo);
	//emit cacheSmartCardNO(m_smartCard);
}

void ReadAgency::recevieSmartcardNORequest(int stat, QString data)
{
	qDebug() << "stat=" << stat << endl;
	if (stat != 200)
	{
		emit cacheBindTip(QStringLiteral("请检查网络"));
		//qDebug() << data << endl;
		return;
	}
	qDebug() << data << endl;
	QJsonParseError error;
	QJsonDocument jsonDocument = QJsonDocument::fromJson(data.toUtf8(), &error);
	if (error.error == QJsonParseError::NoError) {
		if (!(jsonDocument.isNull() || jsonDocument.isEmpty()))
		{
			if (jsonDocument.isObject())
			{
				QVariantMap result = jsonDocument.toVariant().toMap();
				int stat = result["code"].toInt();
				switch (stat)
				{
				case 0:
				{
						  isValidWatchCardNO = true;
						  emit cacheSmartCardInfo(m_smartCard, QStringLiteral("未绑定"));
						  emit cacheBindTip("");
						  if (isValidIdCardNO)
						  {
							  StartBind();
						  }
						  /*else
						  {
							  emit cacheStudentInfo("", "", "", "");
						  }*/
						  break;
				}
				case 1:
				{
						  isValidWatchCardNO = false;
						  emit cacheBindTip(m_smartCard+QStringLiteral("已绑定"));
						  //emit cacheStudentInfo("", "", "", "");
						  //emit cacheSmartCardInfo("", "");
						  StartUnBind();
						  //isValidWatchCardNO = true;

						  break;
				}
				case 2:
				{
						  isValidWatchCardNO = false;
						  emit cacheBindTip(QStringLiteral("腕表") + m_smartCard + QStringLiteral("损坏或者在维修"));
						  emit cacheSmartCardInfo("", "");
						  break;
				}
				case 3:
				{
						  isValidWatchCardNO = false;
						  emit sigAddWatch(m_smartCard);
						  emit cacheSmartCardInfo("", "");
						  emit cacheStudentInfo("", "", "", "");
						  isValidIdCardNO = false;
						  break;
				}
				case 4:
				{
						  isValidWatchCardNO = false;
						  emit cacheBindTip(QStringLiteral("腕表") + m_smartCard + QStringLiteral("对应的学生未找到"));
						  emit cacheSmartCardInfo("", "");
						  StartUnBind();
						  break;
				}
				default:
					break;
				}
			}
			else
			{

			}
		}
	}
	else {
		//qFatal(error.errorString().toUtf8().constData());
		//exit(1);
	}
}

void ReadAgency::StopTime()
{
	m_readIDCard->StopTime();
	m_readSmartCard->StopTime();
	//m_checkConnClock->stop();
}

void ReadAgency::StartTime()
{
	m_readIDCard->StartTime();
	m_readSmartCard->StartTime();
	//m_checkConnClock->start();
}

void ReadAgency::StartBind()
{
	type = Bind;
	if (isValidIdCardNO && isValidWatchCardNO)
	{
		request.StartBind(m_idCardNO, m_smartCard, Bind);
	}
}

void ReadAgency::StartUnBind()
{
	type = UnBind;
	request.StartUnBind(m_smartCard, UnBind);
}

void ReadAgency::recevieBindRequest(int stat, QString data)
{
	if (type != Bind)
		return;
	qDebug() << "stat=" << stat << endl;
	if (stat != 200)
	{
		BindStat = false;
		emit cacheBindTip(QStringLiteral("请检查网络"));
		return;
	}
	qDebug() << data << endl;
	QJsonParseError error;
	QJsonDocument jsonDocument = QJsonDocument::fromJson(data.toUtf8(), &error);
	if (error.error == QJsonParseError::NoError) {
		if (!(jsonDocument.isNull() || jsonDocument.isEmpty()))
		{
			if (jsonDocument.isObject())
			{
				QVariantMap result = jsonDocument.toVariant().toMap();
				int stat = result["code"].toInt();
				switch (stat)
				{
				case 0:
				{
						  emit cacheBindTip(QStringLiteral("绑定失败"));
						  emit cacheStudentInfo("", "", "", "");
						  BindStat = false;
						  break;
				}
				case 1:
				{
						  BindStat = true;
						  emit cacheSmartCardInfo(m_smartCard, QStringLiteral("已绑定"));
						  BindStat = false;
						  break;
				}
				case 2:
				{
						  emit cacheBindTip(QStringLiteral("这场考试为找到该学生"));
						  emit cacheStudentInfo("", "", "", "");
						  BindStat = false;
						  isValidIdCardNO = false;
						  break;
				}
				case 3:
				{
						  emit cacheBindTip(QStringLiteral("未找到该学生"));
						  emit cacheStudentInfo("", "", "", "");
						  BindStat = false;
						  isValidIdCardNO = false;
						  break;
				}
				case 4:
				{
						  emit cacheBindTip(QStringLiteral("未找到考场"));
						  emit cacheStudentInfo("", "", "", "");
						  emit cacheSmartCardInfo("", "");
						  isValidIdCardNO = false;
						  isValidWatchCardNO = false;
						  BindStat = false;
						  break;
				}
				default:
					break;
				}
			}
			else
			{
				BindStat = false;
			}
		}
	}
	else {
		//qFatal(error.errorString().toUtf8().constData());
		//exit(1);
	}
}

void ReadAgency::recevieUnBindRequest(int stat, QString data)
{
	if (type != UnBind)
		return;
	qDebug() << "stat=" << stat << endl;
	if (stat != 200)
	{
		UnBindStat = false;
		emit cacheBindTip(QStringLiteral("请检查网络"));
		return;
	}
	qDebug() << data << endl;
	QJsonParseError error;
	QJsonDocument jsonDocument = QJsonDocument::fromJson(data.toUtf8(), &error);
	if (error.error == QJsonParseError::NoError) {
		if (!(jsonDocument.isNull() || jsonDocument.isEmpty()))
		{
			if (jsonDocument.isObject())
			{
				QVariantMap result = jsonDocument.toVariant().toMap();
				int stat = result["code"].toInt();
				switch (stat)
				{
				case 0:
				{
						  emit cacheSmartCardInfo(m_smartCard, QStringLiteral("解绑失败请重刷"));
						  emit cacheStudentInfo("", "", "", "");
						  UnBindStat = false;
						  break;
				}
				case 1:
				{
						  UnBindStat = true;
						  emit cacheBindTip(QStringLiteral("腕表") + m_smartCard + QStringLiteral("已解绑"));
						  emit cacheSmartCardInfo(m_smartCard, QStringLiteral("未绑定"));
						  BindStat = false;
						  isValidWatchCardNO = true;
						  break;
				}
				case 2:
				{
						  UnBindStat = true;
						  emit cacheBindTip(QStringLiteral("腕表") + m_smartCard + QStringLiteral("已解绑"));
						  emit cacheSmartCardInfo(m_smartCard, QStringLiteral("未绑定"));
						  BindStat = false;
						  isValidWatchCardNO = true;
						  break;
				}
				default:
					break;
				}
			}
			else
			{
				UnBindStat = false;
			}
		}
	}
	else {
		//qFatal(error.errorString().toUtf8().constData());
		//exit(1);
	}
}