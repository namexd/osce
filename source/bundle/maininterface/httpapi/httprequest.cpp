#include "httprequest.h"

HttpRequest::HttpRequest(QObject* parent) :QObject(parent)
{
	currentType = -1;
	grant_type = QStringLiteral("password");
	loginApi = QStringLiteral("/api/1.0/public/oauth/access_token");
	examApi = QStringLiteral("/api/1.0/private/osce/watch/exam-list");
	studentInfoApi = QStringLiteral("/api/1.0/private/osce/watch/student-details");
	watchStatusApi = QStringLiteral("/api/1.0/private/osce/watch/watch-status");
	bindApi = QStringLiteral("/api/1.0/private/osce/watch/bound-watch");
	unbindApi = QStringLiteral("/api/1.0/private/osce/watch/unwrap-watch");
	getAllWatchApi = QStringLiteral("/api/1.0/private/osce/watch/list");
	getWatchSearchApi = QStringLiteral("/api/1.0/private/osce/watch/list");
	addWatchApi = QStringLiteral("/api/1.0/private/osce/watch/add");
	modWatchApi = QStringLiteral("/api/1.0/private/osce/watch/update");
	getWatchByCodeApi = QStringLiteral("/api/1.0/private/osce/watch/watch-detail");
	delWatchApi = QStringLiteral("/api/1.0/private/osce/watch/delete");
}

HttpRequest::~HttpRequest()
{

}

void HttpRequest::setUserInfo(QString access_token, QString token_type, QString expires_in, QString user_id)
{
	this->access_token = access_token;
	this->token_type = token_type;
	this->expires_in = expires_in;
	this->user_id = user_id;
}

void HttpRequest::setExamID(QString id, QString name)
{
	this->exam_id = id;
	this->exam_name = name;
	sigExamNam(name);
}

QString HttpRequest::getExam()
{
	return exam_name;
}


void HttpRequest::Login(QString serverip, QString username, QString password, QString client_id, QString client_secret, int type)
{
	currentType = type;
	this->serverip = serverip;
	this->username = username;
	this->password = password;
	this->client_id = client_id;
	this->client_secret = client_secret;
	QString szUserName = QString("username=") + username.trimmed();
	QString szPassword = QString("password=") + password.trimmed();
	QString szGrant_type = QString("grant_type=") + grant_type.trimmed();
	QString szClient_id = QString("client_id=") + client_id.trimmed();
	QString szClient_secret = QString("client_secret=") + client_secret.trimmed();

	QString post = szUserName + QString("&") + szPassword + QString("&") + szGrant_type + QString("&") + szClient_id + QString("&") + szClient_secret;
	QByteArray post_data = post.toUtf8();

	QNetworkAccessManager* httpAccessLogin = new QNetworkAccessManager(this);
	QNetworkRequest request;
	QString url = QStringLiteral("http://") + serverip + loginApi;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccessLogin->post(request, post_data);

	connect(httpAccessLogin, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::getExamList(int type)
{
	currentType = type;
	QString token = QString("access_token=") + this->access_token.trimmed();
	QString requry = token;
	QString preUrl = QStringLiteral("http://") + serverip + examApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}



void HttpRequest::HttpReturn(QNetworkReply* reply)
{
	QVariant statusCodeV = reply->attribute(QNetworkRequest::HttpStatusCodeAttribute);
	emit requestData(statusCodeV.toInt(), QString::fromUtf8(reply->readAll()),currentType);
	reply->deleteLater();
}

void HttpRequest::getStudentInfoByIDCard(QString id, int type)
{
	currentType = type;
	QString szID = QString("id_card=") + id.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();
	QString requry = szID + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + studentInfoApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}


void HttpRequest::getSmartByCardID(QString id, int type)
{
	currentType = type;
	QString szID = QString("code=") + id.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();
	QString requry = szID + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + watchStatusApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::StartBind(QString idcard, QString watchid, int type)
{
	currentType = type;
	QString szID = QString("id_card=") + idcard.trimmed();
	QString szCode = QString("code=") + watchid.trimmed();
	QString szExam = QString("exam_id=") + this->exam_id.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();
	QString requry = szID + QString("&") + szCode + QString("&") + szExam + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + bindApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::StartUnBind(QString watchid, int type)
{
	currentType = type;
	QString szID = QString("code=") + watchid.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();
	QString requry = szID + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + unbindApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

//Íó±í¹ÜÀí
void HttpRequest::GetWatchByManager(int type)
{
	currentType = type;
	QString token = QString("access_token=") + this->access_token.trimmed();
	QString requry = token;
	QString preUrl = QStringLiteral("http://") + serverip + getAllWatchApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::GetWatchByManagerSearch(QString id, QString stat, int type)
{
	currentType = type;
	QString szID = QString("code=") + id.trimmed();
	int st = stat.toInt();
	if (st < 0)
		stat = "";
	QString szStat = QString("status=") + stat.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();

	QString requry; //= szID + QString("&") + szStat + QString("&") + token;
	if (st < 0)
	{
		requry = szID + QString("&") + token;
	}
	else if (st == 0)
	{
		szStat = QString("status=-1");
		requry = szID + QString("&") + szStat + QString("&") + token;
	}
	else
	{
		requry = szID + QString("&") + szStat + QString("&") + token;
	}
	//QString requry = szID + QString("&") + szStat + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + getWatchSearchApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::AddWatchsByManager(QString code, QString name, QString status, QString description, QString factory, QString sp, QString purchase, int type)
{
	currentType = type;
	QString szCode = QString("code=") + code.trimmed();
	QString szName = QString("name=") + name.trimmed();
	QString szStatus = QString("status=") + status.trimmed();
	QString szDescription = QString("description=") + description.trimmed();
	QString szFactory = QString("factory=") + factory.trimmed();
	QString szSp = QString("sp=") + sp.trimmed();
	QString szCreated_user_id = QString("create_user_id=") + this->user_id;
	QString szPurchase_dt = QString("purchase_dt=") + purchase.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();

	QString requry = szCode + QString("&") + szName + QString("&") + szStatus + QString("&") + szDescription + QString("&") + szFactory + QString("&") + szSp + QString("&") + szCreated_user_id + QString("&") + szPurchase_dt + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + addWatchApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::ModWatchsByManager(QString code, QString name, QString status, QString description, QString factory, QString sp, QString purchase, int type)
{
	currentType = type;
	QString szCode = QString("code=") + code.trimmed();
	QString szName = QString("name=") + name.trimmed();
	QString szStatus = QString("status=") + status.trimmed();
	QString szDescription = QString("description=") + description.trimmed();
	QString szFactory = QString("factory=") + factory.trimmed();
	QString szSp = QString("sp=") + sp.trimmed();
	QString szCreated_user_id = QString("create_user_id=") + this->user_id;
	QString szPurchase_dt = QString("purchase_dt=") + purchase.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();


	QString requry = szCode + QString("&") + szName + QString("&") + szStatus + QString("&") + szDescription + QString("&") + szFactory + QString("&") + szSp + QString("&") + szCreated_user_id + QString("&") + szPurchase_dt + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + modWatchApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::GetWatchByManagerByCode(QString code,int type)
{
	currentType = type;
	QString szCode = QString("code=") + code.trimmed();
	QString token = QString("access_token=") + this->access_token.trimmed();

	QString requry = szCode + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + getWatchByCodeApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}

void HttpRequest::DelWatchsByManager(QString code, int type)
{
	currentType = type;
	QString szCode = QString("code=") + code.trimmed();
	QString szCreated_user_id = QString("create_user_id=") + this->user_id;
	QString token = QString("access_token=") + this->access_token.trimmed();

	QString requry = szCode + QString("&") + szCreated_user_id + QString("&") + token;
	QString preUrl = QStringLiteral("http://") + serverip + delWatchApi;
	QString url = preUrl + QString("?") + requry;
	qDebug() << url << endl;

	QNetworkAccessManager* httpAccess = new QNetworkAccessManager(this);
	QNetworkRequest request;
	request.setUrl(QUrl(url));
	request.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
	QNetworkReply* reply = httpAccess->get(request);
	connect(httpAccess, SIGNAL(finished(QNetworkReply*)), this, SLOT(HttpReturn(QNetworkReply*)));
}