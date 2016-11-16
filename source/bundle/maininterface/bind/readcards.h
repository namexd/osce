#ifndef READCARDS
#define READCARDS

#include <QObject>
#include <QString>
#include <QTimer>
#include <QLibrary>
#include <windows.h>
#include "../comment.h"


class CCalcTime
{
public:
	LARGE_INTEGER t1;
	LARGE_INTEGER t2;
	__int64 CountTick;
	__int64 Count;
	__int64 PerSec;
	__int64 ThisTick;
	int ThisMSecond;


	int MSecond;
	int ms[10000];
	int Index;



	CCalcTime()
	{
		Clear();
	}
	~CCalcTime(){};

	void BegginTime(){ QueryPerformanceCounter(&t1); };
	void EndTime() {
		QueryPerformanceCounter(&t2);
		ThisTick = (t2.QuadPart - t1.QuadPart);
		CountTick += ThisTick;
		Count++;
		ThisMSecond = (int)(ThisTick * 1000 / PerSec);
		MSecond = (int)(((double)CountTick) / PerSec * 1000);
		ms[Index++] = (int)((double)(t2.QuadPart - t1.QuadPart) / PerSec * 1000);
		if (Index == 10000) Index = 0;
	};
	void Clear()
	{
		Count = 0;
		CountTick = 0;
		QueryPerformanceFrequency(&t1);
		PerSec = t1.QuadPart;
		MSecond = 0;
		Index = 0;
		memset(ms, 0, sizeof(ms));
		ThisMSecond = 0;
		ThisTick = 0;
	};

};

//----------------------------------------------------------
class ReadIDCard :public QObject
{
	Q_OBJECT
public:
	explicit ReadIDCard(QObject* parent=0);
	~ReadIDCard();
	void StopTime();
	void StartTime();
signals:
	void FindDeviceFail();
	void OpenIDCardPortFail();
	void ReadIDcardFail();
	void SelectIDcardFail();
	void FindIDcardFail();

	void CanReadCard();
	void cacheIDCardNO(QString data,QString name);

	void cacheTip(QString tip);

	void connectDevice(int stat);
public slots :
	void IDCardClockTimeout();
	void FindReadIDCardDevice();
	bool SelectIDCard();
	bool ReadCardNO();
	bool FindIDCard();
	void ReadIDCardNO();
public:
	int m_iPort;
	QString m_idCardNO;
	QString m_stuName;
	QTimer* m_readIDCardClock;
};

//----------------------------------------------------------
class ReadSmartCard :public QObject
{
	Q_OBJECT
public:
	explicit ReadSmartCard(QObject* parent = 0);
	~ReadSmartCard();
	void StopTime();
	void StartTime();
signals:
	void cacheSmartID(QString id);
	void siginitLoadDll();
	void initPort();

	void cacheTip(QString tip);

	void connectDevice(int stat);
public slots :
	void initLoadLib();
	void FindEhuoyanDevice();
	void SearchSmartCard();
private:
	QString HexToQString(unsigned char* pData,int len);
	unsigned short SetHexToAscII(BYTE szHex);
	BYTE GetHexValue(BYTE ch);
	QString ExchangeHexToString(QString strHex);
private:
	int port;
	QLibrary* dllLoad;
	bool isLoadDll;
	bool isHavePort;

	QTimer* m_readSmartCardClock;

	QString data;
};

//----------------------------------------------------------
class ReadAgency :public QObject
{
	Q_OBJECT
public:
	explicit ReadAgency(QObject* parent = 0);
	~ReadAgency();
	void StopTime();
	void StartTime();
signals:
	void cacheIDCardNO(QString data);
	void cacheIDCardTip(QString data);
	void cacheStudentInfo(QString stuName,QString stuNO,QString idNO,QString ticketNO);

	void cacheSmartCardInfo(QString data,QString stat);
	void cacheWatchReadTip(QString data);
	void sigAddWatch(QString data);

	void cacheBindTip(QString data);
	void cacheBindTipIDCard(QString data);
	void cacheBindTipSmartCard(QString data);
public slots :
	void DealIDcardNO(QString data, QString name);
	void recevieIDcardNORequest(int stat, QString data);

	void DealSmartID(QString id);
	void recevieSmartcardNORequest(int stat, QString data);


	void StartBind();
	void StartUnBind();


	void recevieBindRequest(int stat, QString data);
	void recevieUnBindRequest(int stat, QString data);


	void recevieRequest(int stat, QString data,int type);

	void DealIDCardConnect(int stat);
	void DealSmartConnect(int stat);

	void checkDeviceConn();
private:
	void init();

	ReadIDCard* m_readIDCard;
	QString m_idCardNO;
	QString m_stuName;


	QString m_smartCard;

	bool isValidIdCardNO;
	bool isValidWatchCardNO;

	bool BindStat;//绑定过程中的
	bool UnBindStat;//解绑过程中的

	ReadSmartCard* m_readSmartCard;

	int type;

	bool connIDcard;
	bool connSmart;
	QTimer* m_checkConnClock;
};
#endif