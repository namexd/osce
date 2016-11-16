#include "tip.h"
#include <QPainter>
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QFormLayout>
#include <QStyleOption>

BindNoTip::BindNoTip(QWidget* parent) :QWidget(parent)
{
	this->setFixedSize(945, 40);
	this->setStyleSheet("border:0px;background-color:transparent;");
}
BindNoTip::~BindNoTip()
{

}

//------------------------------------------------------------
BindTip::BindTip(QWidget* parent) :QWidget(parent)
{
	m_clearClock = new QTimer(this);
	m_clearClock->setInterval(3000);
	this->setFixedSize(947, 42);
	createUI();
	setLayoutUI();
	setConnection();
}

BindTip::~BindTip()
{

}

void BindTip::createUI()
{
	m_tipic = new ActivityLabel(this);
	m_tipic->setPicName(QString(":/img/bindtip"));

	//m_tip = new QLabel(this);
	//m_tip->setFixedSize(250,30);

	//m_tipWatch = new QLabel(this);
	//m_tipWatch->setFixedSize(250,30);


	m_tipBind = new QLabel(this);
	m_tipBind->setFixedSize(500, 30);

	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("微软雅黑"));
	ft.setBold(true);
	//m_tip->setFont(ft);
	//m_tip->setStyleSheet("color:rgb(170,52,50);");
	//m_tip->setText(QStringLiteral("此为提示消息"));

	//m_tipWatch->setFont(ft);
	//m_tipWatch->setStyleSheet("color:rgb(170,52,50);");
	//m_tipWatch->setText(QStringLiteral("此为提示消息"));

	m_tipBind->setFont(ft);
	m_tipBind->setStyleSheet("color:rgb(170,52,50);");
	//m_tipBind->setText(QStringLiteral("此为提示消息"));

	m_close = new ActivityLabel(this);
	m_close->setPicName(QString(":/img/bindclose"));

	this->setObjectName("BindTip");
}

void BindTip::setLayoutUI()
{
	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addWidget(m_tipic);
	hLayout->addSpacing(10);
	//hLayout->addWidget(m_tip);
	//hLayout->addSpacing(10);
	//hLayout->addWidget(m_tipWatch);
	//hLayout->addSpacing(10);
	hLayout->addWidget(m_tipBind);
	hLayout->addStretch();
	hLayout->addWidget(m_close);
	hLayout->setContentsMargins(20, 0, 20, 0);

	QVBoxLayout* mainLayout = new QVBoxLayout();
	mainLayout->addStretch();
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	mainLayout->setContentsMargins(0, 0, 0, 0);

	this->setLayout(mainLayout);

}

void BindTip::setConnection()
{
	connect(m_close, SIGNAL(Lclicked()), this, SLOT(setPage()));
	connect(m_clearClock, SIGNAL(timeout()), this, SLOT(clearTip()));
}

void BindTip::setPage()
{
	int page = 0;
	emit turnPage(page);
}

void BindTip::setTip(QString tip)
{
	//m_tip->setText(tip);
}

void BindTip::setWatchTip(QString tip)
{
	//m_tipWatch->setText(tip);
}

void BindTip::setBindTip(QString tip)
{
	StopClearClock();
	m_tipBind->setText(tip);
	StartClearClock();
}

void BindTip::clearTip()
{
	m_tipBind->setText("");
}

void BindTip::StartClearClock()
{
	m_clearClock->start();
}
void BindTip::StopClearClock()
{
	m_clearClock->stop();
}
void BindTip::paintEvent(QPaintEvent *)
{
	/*QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(255, 231, 231));*/

	QStyleOption opt;
	opt.init(this);
	QPainter p(this);
	style()->drawPrimitive(QStyle::PE_Widget, &opt, &p, this);
}