#include "bindwidget.h"
#include <QPainter>
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QFormLayout>
#include <QStyleOption>
#include "../httpapi/httprequest.h"

extern HttpRequest request;
//------------------------------------------------------------
CommentBindTopWidget::CommentBindTopWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

CommentBindTopWidget::~CommentBindTopWidget()
{

}

void CommentBindTopWidget::createUI()
{
	m_topborder = new QWidget(this);
	m_topborder->setFixedHeight(3);
	m_topborder->setStyleSheet("border:0px;background-color:rgb(22,190,176);");

	m_pic = new ActivityLabel(this);
	m_pic->setPicName(QString(":/img/watch"));

	m_bottom = new QWidget(this);
	m_bottom->setFixedHeight(1);
	m_bottom->setStyleSheet("border:0px;background-color:rgb(227,227,227);");

	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft.setBold(true);
	m_name = new QLabel(this);
	m_name->setFixedSize(100,30);
	m_name->setFont(ft);
	m_name->setStyleSheet("color:rgb(22,190,176);");

	this->setFixedHeight(34);
}

void CommentBindTopWidget::setLayoutUI()
{
	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addWidget(m_pic);
	hLayout->addSpacing(10);
	hLayout->addWidget(m_name);
	hLayout->addStretch();
	hLayout->setContentsMargins(30,0,0,0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->setSpacing(0);
	mainLayout->addWidget(m_topborder);
	mainLayout->addLayout(hLayout);
	mainLayout->addWidget(m_bottom);
	//mainLayout->addStretch();

	mainLayout->setContentsMargins(0,0,0,0);
	this->setLayout(mainLayout);
}

void CommentBindTopWidget::setConnection()
{

}

void  CommentBindTopWidget::setName(QString sname)
{
	this->name = sname;
	m_name->setText(sname);
}

void CommentBindTopWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(255, 255, 255));
}

//------------------------------------------------------------
StudentInfoWidget::StudentInfoWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

StudentInfoWidget::~StudentInfoWidget()
{

}

void StudentInfoWidget::createUI()
{
	this->setFixedSize(340, 210);
	m_head = new CommentBindTopWidget(this);
	m_head->setName(QStringLiteral("¿¼ÉúÐÅÏ¢"));


	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft.setBold(true);
	//ft.setLetterSpacing(QFont::AbsoluteSpacing, 5);

	QFont ft1;
	ft1.setPointSize(12);
	ft1.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));

	m_name = new QLabel(this);
	m_name->setFixedSize(80, 30);
	m_name->setText(QStringLiteral("ÐÕ  Ãû:"));
	m_name->setAlignment(Qt::AlignVCenter);
	m_name->setFont(ft);
	m_name->setStyleSheet("color:rgb(103,106,108);");

	m_nameEdit = new QLineEdit(this);
	m_nameEdit->setReadOnly(true);
	//m_nameEdit->setText(QStringLiteral("¹ËÑ×Îä"));
	m_nameEdit->setFixedSize(200, 30);
	m_nameEdit->setStyleSheet("background:transparent;border:0px;color:rgb(103,106,108);");
	m_nameEdit->setFont(ft1);
	m_nameEdit->setContextMenuPolicy(Qt::NoContextMenu);

	m_stuNO = new QLabel(this);
	m_stuNO->setFixedSize(80, 30);
	m_stuNO->setText(QStringLiteral("Ñ§  ºÅ:"));
	m_stuNO->setAlignment(Qt::AlignVCenter);
	m_stuNO->setFont(ft);
	m_stuNO->setStyleSheet("color:rgb(103,106,108);");

	m_stuNOEdit =new QLineEdit(this);
	m_stuNOEdit->setReadOnly(true);
	//m_stuNOEdit->setText(QStringLiteral("201013031416"));
	m_stuNOEdit->setFixedSize(200, 30);
	m_stuNOEdit->setStyleSheet("background:transparent;border:0px;color:rgb(103,106,108);");
	m_stuNOEdit->setFont(ft1);
	m_stuNOEdit->setContextMenuPolicy(Qt::NoContextMenu);

	m_idNO = new QLabel(this);
	m_idNO->setFixedSize(80, 30);
	m_idNO->setText(QStringLiteral("Éí·ÝÖ¤:"));
	m_idNO->setAlignment(Qt::AlignVCenter);
	m_idNO->setFont(ft);
	m_idNO->setStyleSheet("color:rgb(103,106,108);");

	m_idNOEdit = new QLineEdit(this);
	m_idNOEdit->setReadOnly(true);
	//m_idNOEdit->setText(QStringLiteral("300200199510090012"));
	m_idNOEdit->setFixedSize(200, 30);
	m_idNOEdit->setStyleSheet("background:transparent;border:0px;color:rgb(103,106,108);");
	m_idNOEdit->setFont(ft1);
	m_idNOEdit->setContextMenuPolicy(Qt::NoContextMenu);

	m_ticktNO = new QLabel(this);
	m_ticktNO->setFixedSize(80, 30);
	m_ticktNO->setText(QStringLiteral("×¼¿¼Ö¤:"));
	m_ticktNO->setAlignment(Qt::AlignVCenter);
	m_ticktNO->setFont(ft);
	m_ticktNO->setStyleSheet("color:rgb(103,106,108);");

	m_ticktNOEdit = new QLineEdit(this);
	m_ticktNOEdit->setReadOnly(true);
	//m_ticktNOEdit->setText(QStringLiteral("201013031416"));
	m_ticktNOEdit->setFixedSize(200, 30);
	m_ticktNOEdit->setStyleSheet("background:transparent;border:0px;color:rgb(103,106,108);");
	m_ticktNOEdit->setFont(ft1);
	m_ticktNOEdit->setContextMenuPolicy(Qt::NoContextMenu);

	
}

void StudentInfoWidget::setLayoutUI()
{
	QHBoxLayout* hName = new QHBoxLayout();
	hName->setMargin(0);
	hName->addWidget(m_name);
	//hName->addSpacing(15);
	hName->addWidget(m_nameEdit);
	//hName->addStretch();
	hName->setContentsMargins(0,0,0,0);

	QHBoxLayout* hStuNO = new QHBoxLayout();
	hStuNO->setMargin(0);
	hStuNO->addWidget(m_stuNO);
	//hStuNO->addSpacing(15);
	hStuNO->addWidget(m_stuNOEdit);
	//hStuNO->addStretch();

	QHBoxLayout* hIdNO = new QHBoxLayout();
	hIdNO->setMargin(0);
	hIdNO->addWidget(m_idNO);
	//hIdNO->addSpacing(15);
	hIdNO->addWidget(m_idNOEdit);
	hIdNO->setContentsMargins(0,0,0,0);

	QHBoxLayout* hTicketNO = new QHBoxLayout();
	hTicketNO->setMargin(0);
	hTicketNO->addWidget(m_ticktNO);
	//hTicketNO->addSpacing(15);
	hTicketNO->addWidget(m_ticktNOEdit);
	hTicketNO->setContentsMargins(0,0,0,0);

	QFormLayout* form = new QFormLayout();
	form->addRow(hName);
	form->addRow(hStuNO);
	form->addRow(hIdNO);
	form->addRow(hTicketNO);
	form->setVerticalSpacing(5);

	QHBoxLayout* hContent = new QHBoxLayout();
	//hContent->addStretch();
	hContent->addLayout(form);
	hContent->addStretch();
	hContent->setContentsMargins(0,0,0,0);

	QVBoxLayout* vContent = new QVBoxLayout();
	vContent->setMargin(0);
	vContent->setSpacing(0);
	vContent->addLayout(hContent);
	vContent->setContentsMargins(30,15,0,0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_head);
	mainLayout->addLayout(vContent);
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(0,0,0,0);
	this->setLayout(mainLayout);
	
}

void StudentInfoWidget::setConnection()
{
	
}

void StudentInfoWidget::setIDCardData(QString stuName, QString stuNO, QString idNO, QString ticketNO)
{
	m_nameEdit->setText(stuName);
	m_stuNOEdit->setText(stuNO);
	m_idNOEdit->setText(idNO);
	m_ticktNOEdit->setText(ticketNO);
}

void StudentInfoWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(255, 255, 255));
}

//------------------------------------------------------------
WristWatchInfoWidget::WristWatchInfoWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

WristWatchInfoWidget::~WristWatchInfoWidget()
{

}

void WristWatchInfoWidget::createUI()
{
	this->setFixedSize(340,210);
	m_head = new CommentBindTopWidget(this);
	m_head->setName(QStringLiteral("Íó±í"));

	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft.setBold(true);
	//ft.setLetterSpacing(QFont::AbsoluteSpacing, 5);

	QFont ft1;
	ft1.setPointSize(12);
	ft1.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));

	QFont ft2;
	ft2.setPointSize(12);
	ft2.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft2.setBold(true);
	ft2.setLetterSpacing(QFont::AbsoluteSpacing, 7);

	m_watchID = new QLabel(this);
	m_watchID->setFixedSize(60, 30);
	m_watchID->setText(QStringLiteral("ID:"));
	m_watchID->setAlignment(Qt::AlignVCenter);
	m_watchID->setFont(ft2);
	m_watchID->setStyleSheet("color:rgb(103,106,108);");

	m_watchIDEdit = new QLineEdit(this);
	m_watchIDEdit->setReadOnly(true);
	//m_watchIDEdit->setText(QStringLiteral("1234566564879"));
	m_watchIDEdit->setFixedSize(200, 30);
	m_watchIDEdit->setStyleSheet("background:transparent;border:0px;color:rgb(103,106,108);");
	m_watchIDEdit->setFont(ft1);
	m_watchIDEdit->setContextMenuPolicy(Qt::NoContextMenu);

	m_watchStat = new QLabel(this);
	m_watchStat->setFixedSize(60, 30);
	m_watchStat->setText(QStringLiteral("×´Ì¬:"));
	m_watchStat->setAlignment(Qt::AlignVCenter);
	m_watchStat->setFont(ft);
	m_watchStat->setStyleSheet("color:rgb(103,106,108);");

	m_watchStatEdit = new QLineEdit(this);
	m_watchStatEdit->setReadOnly(true);
	//m_watchStatEdit->setText(QStringLiteral("ÒÑ°ó¶¨"));
	m_watchStatEdit->setFixedSize(200, 30);
	m_watchStatEdit->setStyleSheet("background:transparent;border:0px;color:rgb(103,106,108);");
	m_watchStatEdit->setFont(ft1);
	m_watchStatEdit->setContextMenuPolicy(Qt::NoContextMenu);
}

void WristWatchInfoWidget::setLayoutUI()
{
	QHBoxLayout* hID = new QHBoxLayout();
	hID->setMargin(0);
	hID->addWidget(m_watchID);
	//hID->addSpacing(15);
	hID->addWidget(m_watchIDEdit);
	hID->addStretch();
	hID->setContentsMargins(0, 0, 0, 0);

	QHBoxLayout* hStat = new QHBoxLayout();
	hStat->setMargin(0);
	hStat->addWidget(m_watchStat);
	//hStat->addSpacing(15);
	hStat->addWidget(m_watchStatEdit);
	hStat->addStretch();
	hStat->setContentsMargins(0, 0, 0, 0);

	QFormLayout* form = new QFormLayout();
	form->addRow(hID);
	form->addRow(hStat);
	form->setVerticalSpacing(5);

	QHBoxLayout* hContent = new QHBoxLayout();
	//hContent->addStretch();
	hContent->addLayout(form);
	hContent->addStretch();
	hContent->setContentsMargins(0, 0, 0, 0);

	QVBoxLayout* vContent = new QVBoxLayout();
	vContent->setMargin(0);
	vContent->setSpacing(0);
	vContent->addLayout(hContent);
	vContent->setContentsMargins(30, 15, 0, 0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_head);
	mainLayout->addLayout(vContent);
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(0, 0, 0, 0);
	this->setLayout(mainLayout);
}

void WristWatchInfoWidget::setConnection()
{

}

void WristWatchInfoWidget::setSmartCardData(QString NO, QString Stat)
{
	m_watchIDEdit->setText(NO);
	m_watchStatEdit->setText(Stat);
}

void WristWatchInfoWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(255, 255, 255));
}
//------------------------------------------------------------
BindWidget::BindWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

BindWidget::~BindWidget()
{

}

void BindWidget::createUI()
{
	m_showExam = new QLabel(this);
	m_showExam->setText(QStringLiteral("µ±Ç°¿¼³¡: "));
	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft.setBold(true);

	m_showExam->setStyleSheet("color:rgb(103,106,108);");
	m_showExam->setFixedSize(200,30);
	m_showExam->setFont(ft);
	m_showExam->setAlignment(Qt::AlignCenter);

	m_tip = new BindTip(this);
	m_notip = new BindNoTip(this);

	m_stackedWidget = new QStackedWidget(this);
	QPalette palette;
	palette.setBrush(QPalette::Window, QBrush(QColor(243, 243, 243)));
	m_stackedWidget->setPalette(palette);
	m_stackedWidget->setAutoFillBackground(true);
	m_stackedWidget->setSizePolicy(QSizePolicy::Fixed, QSizePolicy::Fixed);

	m_stackedWidget->addWidget(m_tip);
	m_stackedWidget->addWidget(m_notip);

	m_stackedWidget->setCurrentWidget(m_tip);
	//m_tip->setStyleSheet("border:1px solid rgb(246,205,205); border-radius: 4px;");


	m_studentInfo = new StudentInfoWidget(this);
	m_watchInfo = new WristWatchInfoWidget(this);
	m_bind = new ActivityLabel(this);
	m_bind->setPicName(QString(":/img/bind"));
	m_watchManager = new ActivityLabel(this);
	m_watchManager->setPicName(QString(":/img/watchmanager"));

	m_readAgency = new ReadAgency(this);
}

void BindWidget::setLayoutUI()
{
	QHBoxLayout* hShowExam = new QHBoxLayout();
	hShowExam->setMargin(0);
	hShowExam->addStretch();
	hShowExam->addWidget(m_showExam);
	hShowExam->addStretch();
	hShowExam->setContentsMargins(0,20,0,0);

	QHBoxLayout* hBindTip = new QHBoxLayout();
	hBindTip->setMargin(0);
	hBindTip->addStretch();
	hBindTip->addWidget(m_stackedWidget);
	hBindTip->addStretch();
	hBindTip->setContentsMargins(0,30,0,0);

	QVBoxLayout* vBindFlag = new QVBoxLayout();
	vBindFlag->setMargin(0);
	vBindFlag->addStretch();
	vBindFlag->addWidget(m_bind);
	vBindFlag->addStretch();
	vBindFlag->setSpacing(0);
	vBindFlag->setContentsMargins(0,0,0,0);

	QHBoxLayout* hWatchManager = new QHBoxLayout();
	hWatchManager->setMargin(0);
	hWatchManager->addStretch();
	hWatchManager->addWidget(m_watchManager);
	hWatchManager->addStretch();
	hWatchManager->setSpacing(0);
	hWatchManager->setContentsMargins(0,0,0,0);

	QHBoxLayout* hBind = new QHBoxLayout();
	hBind->setMargin(0);
	hBind->addStretch();
	hBind->addWidget(m_studentInfo);
	hBind->addLayout(vBindFlag);
	hBind->addWidget(m_watchInfo);
	hBind->addStretch();
	hBind->setSpacing(0);
	hBind->setContentsMargins(0,50,0,0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addLayout(hShowExam);
	mainLayout->addLayout(hBindTip);
	mainLayout->addLayout(hBind);
	mainLayout->addLayout(hWatchManager);
	mainLayout->addStretch();
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(0,0,0,0);
	this->setLayout(mainLayout);

}

void BindWidget::setConnection()
{
	connect(m_tip, SIGNAL(turnPage(int)), this, SLOT(turnPage(int)));
	connect(m_watchManager, SIGNAL(Lclicked()), this, SLOT(setMainContentPage()));

	connect(m_readAgency, SIGNAL(cacheStudentInfo(QString, QString, QString, QString)), this, SLOT(cacheIDCardData(QString, QString, QString, QString)));
	connect(m_readAgency, SIGNAL(cacheIDCardTip(QString)), this, SLOT(popTip(QString)));
	connect(m_readAgency, SIGNAL(cacheWatchReadTip(QString)), this, SLOT(popWatch(QString)));
	connect(m_readAgency, SIGNAL(cacheBindTip(QString)), this, SLOT(popBind(QString)));

	connect(m_readAgency, SIGNAL(cacheSmartCardInfo(QString, QString)), this, SLOT(cacheSmartCardData(QString, QString)));
	connect(m_readAgency, SIGNAL(sigAddWatch(QString)), this, SLOT(changeAddWatch(QString)));
	connect(&request, SIGNAL(sigExamNam(QString)), this, SLOT(getExam(QString)));
}

void BindWidget::getExam(QString name)
{
	m_showExam->setText(QStringLiteral("µ±Ç°¿¼³¡: ")+name);
}

void BindWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(243, 243, 243));
}

void BindWidget::turnPage(int curIndex)
{
	switch (curIndex)
	{
		case 0:
			m_stackedWidget->setCurrentWidget(m_notip);
			break;
		case 1:
		{
			m_stackedWidget->setCurrentWidget(m_tip);
			break;
		}
		default:
			break;
	}
}

void BindWidget::setMainContentPage()
{
	int page = 1;
	emit turnMainContentPage(page);
}

void BindWidget::popTip(QString tip)
{
	//m_tip->setTip(tip);
	//turnPage(1);
}

void BindWidget::cacheIDCardData(QString stuName, QString stuNO, QString idNO, QString ticketNO)
{
	m_tip->setTip("");
	//m_tip->setBindTip("");
	m_studentInfo->setIDCardData(stuName, stuNO, idNO, ticketNO);
}

void BindWidget::cacheSmartCardData(QString NO, QString Stat)
{
	m_tip->setWatchTip("");
	//if (NO.isEmpty() && Stat.isEmpty())
		//m_tip->setBindTip("");
	m_watchInfo->setSmartCardData(NO, Stat);
}

void BindWidget::StopTime()
{
	m_readAgency->StopTime();
}

void BindWidget::StartTime()
{
	m_readAgency->StartTime();
}

void BindWidget::popWatch(QString tip)
{
	//m_tip->setWatchTip(tip);
	//turnPage(1);
}

void BindWidget::popBind(QString tip)
{
	m_tip->setBindTip(tip);
	turnPage(1);
}

void BindWidget::changeAddWatch(QString data)
{
	int page = 1;
	emit turnMainContentPage(page);
	emit SigAddWatch(data);
}