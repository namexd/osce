#include "loginwidget.h"
#include <QHBoxLayout>
#include <QVBoxLayout>
#include <QPainter>
#include <QJsonObject>
#include <QJsonDocument>
#include <QJsonParseError>

#include "../httpapi/httprequest.h"
extern HttpRequest request;

LoginContentWidget::LoginContentWidget(QWidget* parent) :QWidget(parent)
{
	isRemeber = false;
	createUI();
	setLayoutUI();
	setConnection();
}

LoginContentWidget::~LoginContentWidget()
{

}

void LoginContentWidget::createUI()
{
	QFont ft1;
	ft1.setPointSize(12);
	ft1.setFamily(QStringLiteral("Î¢ÈíÑÅºÚ"));
	ft1.setBold(true);

	QFont ft2;
	ft2.setPointSize(12);
	ft2.setFamily(QStringLiteral("Î¢ÈíÑÅºÚ"));

	QFont ft3;
	ft3.setPointSize(10);
	ft3.setFamily(QStringLiteral("Î¢ÈíÑÅºÚ"));

	m_head = new QWidget(this);
	m_head->setFixedHeight(3);
	m_head->setStyleSheet("border:0px;background-color:rgb(22,190,176);");

	m_serverip = new QLabel(this);
	m_serverip->setFixedSize(280,30);
	m_serverip->setText(QStringLiteral("·þÎñÆ÷µØÖ·"));
	m_serverip->setStyleSheet("color:rgb(103,106,108);");
	m_serverip->setAlignment(Qt::AlignLeft|Qt::AlignVCenter);
	m_serverip->setFont(ft1);

	m_serveripInput = new QLineEdit(this);
	m_serveripInput->setFixedSize(280,45);
	m_serveripInput->setStyleSheet("border:1px solid rgb(229,230,231);border-radius:4px;color:rgb(184,184,184);");
	m_serveripInput->setAlignment(Qt::AlignVCenter);
	m_serveripInput->setTextMargins(10, 0, 0, 0);
	m_serveripInput->setFont(ft2);
	m_serveripInput->setContextMenuPolicy(Qt::NoContextMenu);

	m_user = new QLabel(this);
	m_user->setFixedSize(280,30);
	m_user->setText(QStringLiteral("ÓÃ»§Ãû"));
	m_user->setStyleSheet("color:rgb(103,106,108);");
	m_user->setAlignment(Qt::AlignLeft | Qt::AlignVCenter);
	m_user->setFont(ft1);

	m_userInput = new QLineEdit(this);
	m_userInput->setFixedSize(280,45);
	m_userInput->setStyleSheet("border:1px solid rgb(229,230,231);border-radius:4px;color:rgb(184,184,184);");
	m_userInput->setAlignment(Qt::AlignVCenter);
	m_userInput->setTextMargins(10, 0, 0, 0);
	m_userInput->setFont(ft2);
	m_userInput->setContextMenuPolicy(Qt::NoContextMenu);

	m_password = new QLabel(this);
	m_password->setFixedSize(280,30);
	m_password->setText(QStringLiteral("ÃÜÂë"));
	m_password->setStyleSheet("color:rgb(103,106,108);");
	m_password->setAlignment(Qt::AlignLeft | Qt::AlignVCenter);
	m_password->setFont(ft1);

	m_passwordInput = new QLineEdit(this);
	m_passwordInput->setFixedSize(280,45);
	m_passwordInput->setStyleSheet("border:1px solid rgb(229,230,231);border-radius:4px;color:rgb(184,184,184);");
	m_passwordInput->setAlignment(Qt::AlignVCenter);
	m_passwordInput->setTextMargins(10, 0, 0, 0);
	m_passwordInput->setFont(ft2);
	m_passwordInput->setContextMenuPolicy(Qt::NoContextMenu);
	m_passwordInput->setEchoMode(QLineEdit::Password);

	m_remeberPic = new ActivityLabel(this);
	//m_remeberPic->setFixedSize(20,20);
	m_remeberPic->setPicName(QString(":/img/noremebercheck"));


	m_remeberTag = new QLabel(this);
	m_remeberTag->setFixedSize(70,20);
	m_remeberTag->setAlignment(Qt::AlignVCenter | Qt::AlignLeft);
	m_remeberTag->setText(QStringLiteral("¼Ç×¡ÃÜÂë"));
	m_remeberTag->setStyleSheet("border:0px;color:rgb(184,184,184);");
	m_remeberTag->setFont(ft3);

	/*m_forgetPass = new Label(this);
	m_forgetPass->setFixedSize(75, 20);
	m_forgetPass->setAlignment(Qt::AlignVCenter | Qt::AlignRight);
	m_forgetPass->setText(QStringLiteral("Íü¼ÇÃÜÂë?"));
	m_forgetPass->setStyleSheet("border:0px;color:rgb(184,184,184);");
	m_forgetPass->setFont(ft3);*/

	m_login = new ActivityLabel(this);
	m_login->setPicName(QString(":/img/login"));

	m_serveripInput->setText(QStringLiteral("192.168.1.205"));
	m_userInput->setText(QStringLiteral("13699456588"));
	m_passwordInput->setText(QStringLiteral("123456"));
	this->setFixedSize(360,440);
}

void LoginContentWidget::setLayoutUI()
{
	QVBoxLayout* v1 = new QVBoxLayout();
	v1->setMargin(0);
	v1->addWidget(m_serverip);
	v1->addWidget(m_serveripInput);
	v1->setSpacing(0);
	v1->setContentsMargins(0,0,0,0);

	QVBoxLayout* v2 = new QVBoxLayout();
	v2->setMargin(0);
	v2->addWidget(m_user);
	v2->addWidget(m_userInput);
	v2->setSpacing(0);
	v2->setContentsMargins(0,0,0,0);
	
	QVBoxLayout* v3 = new QVBoxLayout();
	v3->setMargin(0);
	v3->addWidget(m_password);
	v3->addWidget(m_passwordInput);
	v3->setSpacing(0);
	v3->setContentsMargins(0,0,0,0);
	
	QHBoxLayout* h1 = new QHBoxLayout();
	h1->setMargin(0);
	h1->addWidget(m_remeberPic);
	h1->addWidget(m_remeberTag);
	h1->setSpacing(2);
	h1->setContentsMargins(0, 0, 0, 0);

	QHBoxLayout* h4 = new QHBoxLayout();
	h4->setMargin(0);
	h4->addLayout(h1);
	h4->addStretch();
	//h4->addWidget(m_forgetPass);
	h4->setContentsMargins(0,0,0,0);

	QVBoxLayout* v4 = new QVBoxLayout();
	v4->setMargin(0);
	v4->addStretch();
	v4->addLayout(h4);
	v4->addStretch();
	v4->setContentsMargins(0,0,0,0);

	QVBoxLayout* vConetent = new QVBoxLayout();
	vConetent->setMargin(0);
	vConetent->addLayout(v1);
	vConetent->addLayout(v2);
	vConetent->addLayout(v3);
	vConetent->addLayout(v4);
	vConetent->addWidget(m_login);
	vConetent->setSpacing(20);
	vConetent->setContentsMargins(0,0,0,0);

	QHBoxLayout* hContent = new QHBoxLayout();
	hContent->setMargin(0);
	hContent->addStretch();
	hContent->addLayout(vConetent);
	hContent->addStretch();
	hContent->setContentsMargins(0,0,0,0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_head);
	mainLayout->addStretch();
	mainLayout->addLayout(hContent);
	mainLayout->addStretch();
	mainLayout->setContentsMargins(0,0,0,0);

	this->setLayout(mainLayout);
}

void LoginContentWidget::setConnection()
{
	connect(&request, SIGNAL(requestData(int, QString, int)), this, SLOT(recevieRequest(int, QString, int)));
	connect(m_remeberPic, SIGNAL(Lclicked()), this, SLOT(changeRemeber()));
	connect(m_login, SIGNAL(Lclicked()), this, SLOT(login()));
}

void LoginContentWidget::changeRemeber()
{
	if (isRemeber)
	{
		isRemeber = false;
		m_remeberPic->setPicName(QString(":/img/noremebercheck"));
	}
	else
	{
		isRemeber = true;
		m_remeberPic->setPicName(QString(":/img/remebercheck"));
	}
}

void LoginContentWidget::login()
{
	QString serverip = m_serveripInput->text();
	if (serverip.isEmpty())
		return;
	QString user = m_userInput->text();
	if (user.isEmpty())
		return;
	QString password = m_passwordInput->text();
	if (password.isEmpty())
		return;
	QString client_id = QStringLiteral("ios");
	QString client_secret = QStringLiteral("111");
	request.Login(serverip, user, password, client_id, client_secret, Login);
}

void LoginContentWidget::recevieRequest(int stat, QString data, int type)
{
	if (type == Login)
		DealLogin(stat,data);
	if (type == ExamGet)
		DealExamList(stat, data);

}

void LoginContentWidget::requstExamList()
{
	request.getExamList(ExamGet);
}

void LoginContentWidget::DealLogin(int stat, QString data)
{

	qDebug() << "stat=" << stat << endl;
	if (stat != 200)
	{
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
				QString access_token = result["access_token"].toString();
				QString token_type = result["token_type"].toString();
				QString expires_in = result["expires_in"].toString();
				QString user_id = result["user_id"].toString();

				request.setUserInfo(access_token, token_type, expires_in, user_id);
				requstExamList();
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

void LoginContentWidget::DealExamList(int stat, QString data)
{
	//"{\"code\":1,\"message\":\"success\",\"data\":{\"total\":1,\"pagesize\":1,\"page\":1,\"rows\":{}}}"
	qDebug() << "stat=" << stat << endl;
	if (stat != 200)
	{
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
				int code = result["code"].toInt();
				if (code == 4)
				{
					emit sigNoExam(QStringLiteral("½ñÈÕÎÞ¿¼ÊÔ"));
					emit turnMainContentPage(1);
					return;
				}
				qDebug() << code << endl;
				if (code != 1)
					return;
				QVariantMap result1 = result["data"].toMap();
				int total = result1["total"].toInt();

				qDebug() << "total=" << total << endl;
				if (total <= 0)
					return;
				if (total == 1)
				{
					QVariantList litt = result1["rows"].toList();
					QVariantMap listmap = litt.at(0).toMap();
					QString id = listmap["id"].toString();
					QString name = listmap["exam_name"].toString();
					request.setExamID(id,name);
					emit turnMainContentPage(0);
					return;
				}
				QVariantList litt = result1["rows"].toList();
				QStringList examid;
				QStringList examname;
				foreach(QVariant plugin, litt)
				{
					QVariantMap listmap = plugin.toMap();
					QString id = listmap["id"].toString();
					QString name = listmap["exam_name"].toString();

					examid.append(id);
					examname.append(name);
				}
				emit setExamViewData(examid,examname);
				//emit turnMainContentPage(0);
				emit turnMainContentPage(3);
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

void LoginContentWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(255, 255, 255));
}

//----------------------------------
LoginWidget::LoginWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

LoginWidget::~LoginWidget()
{

}

void LoginWidget::createUI()
{
	/*m_tip = new BindTip(this);
	m_notip = new BindNoTip(this);

	m_stackedWidget = new QStackedWidget(this);
	QPalette palette;
	palette.setBrush(QPalette::Window, QBrush(QColor(243, 243, 243)));
	m_stackedWidget->setPalette(palette);
	m_stackedWidget->setAutoFillBackground(true);
	m_stackedWidget->setSizePolicy(QSizePolicy::Fixed, QSizePolicy::Fixed);

	m_stackedWidget->addWidget(m_tip);
	m_stackedWidget->addWidget(m_notip);

	m_stackedWidget->setCurrentWidget(m_tip);*/

	m_content = new LoginContentWidget(this);
}

void LoginWidget::setLayoutUI()
{
	/*QHBoxLayout* hBindTip = new QHBoxLayout();
	hBindTip->setMargin(0);
	hBindTip->addStretch();
	hBindTip->addWidget(m_stackedWidget);
	hBindTip->addStretch();
	hBindTip->setContentsMargins(0, 50, 0, 0);*/

	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addStretch();
	hLayout->addWidget(m_content);
	hLayout->addStretch();
	hLayout->setContentsMargins(0,0,0,0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	//mainLayout->addLayout(hBindTip);
	//mainLayout->addSpacing(50);
	mainLayout->addStretch();
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	hLayout->setContentsMargins(0,0,0,0);

	this-> setLayout(mainLayout);
}

void LoginWidget::setConnection()
{
	connect(m_content, SIGNAL(turnMainContentPage(int)), this, SIGNAL(turnMainContentPage(int)));
	connect(m_content, SIGNAL(setExamViewData(QStringList, QStringList)), this, SIGNAL(setExamViewData(QStringList, QStringList)));
	connect(m_content, SIGNAL(sigNoExam(QString)), this, SIGNAL(sigNoExam(QString)));
}

void LoginWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(243, 243, 243));
}