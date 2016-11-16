#ifndef HTTPREQUEST
#define HTTPREQUEST


#include <QString>
#include <QObject>
#include <QNetworkRequest>
#include <QNetworkReply>
#include <QJsonObject>
#include <QJsonDocument>
#include <QJsonParseError>

typedef enum RequestType
{
	GetStudentInfo = 0,
	GetWatchInfo = 1,
	Bind = 2,
	UnBind = 3,
	WatchManagerGet = 4,
	WatchManagerAdd = 5,
	WatchManagerMod = 6,
	WatchManagerDel = 7,
	WatchManagerSea = 8,
	WatchManagerDet = 9,//根据code查详情
	Login = 10,
	ExamGet=11,
};
class HttpRequest :public QObject
{
	Q_OBJECT
public:
	explicit HttpRequest(QObject* parent = 0);
	~HttpRequest();
	void setUserInfo(QString access_token, QString token_type, QString expires_in, QString user_id);
	void setExamID(QString id, QString name);
	QString getExam();
public:
	void Login(QString serverip, QString username, QString password, QString client_id, QString client_secret,int type);
	void getExamList(int type);


	void getStudentInfoByIDCard(QString id, int type);

	void getSmartByCardID(QString id, int type);

	void StartBind(QString idcard, QString watchid, int type);
	void StartUnBind(QString watchid, int type);

	//腕表管理
	void GetWatchByManager(int type);
	void GetWatchByManagerSearch(QString id,QString stat,int type);
	void AddWatchsByManager(QString code, QString name, QString status, QString description, QString factory, QString sp,QString purchase,int type);
	void ModWatchsByManager(QString code, QString name, QString status, QString description, QString factory, QString sp, QString purchase, int type);
	void GetWatchByManagerByCode(QString code,int type);
	void DelWatchsByManager(QString code,int type);


signals:
	void requestData(int stat, QString data, int type);

	void sigExamNam(QString name);

public slots:
	void HttpReturn(QNetworkReply*);

private:
	QString username;
	QString password;
	QString grant_type;
	QString client_id;
	QString client_secret;
	QString serverip;

	//登录的返回
	QString access_token;
	QString token_type;
	QString expires_in;
	QString user_id;


	QString exam_id;
	QString exam_name;
	int currentType;

	//api地址
	QString loginApi;
	QString examApi;
	QString studentInfoApi;
	QString watchStatusApi;
	QString bindApi;
	QString unbindApi;
	QString getAllWatchApi;
	QString getWatchSearchApi;
	QString addWatchApi;
	QString modWatchApi;
	QString getWatchByCodeApi;
	QString delWatchApi;
};

#endif