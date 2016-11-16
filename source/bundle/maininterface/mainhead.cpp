#include "mainhead.h"
#include <QHBoxLayout>
#include <QPainter>
#include "mainwidget.h"

MainHead::MainHead(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
	mouse_press = false;
}

MainHead::~MainHead()
{

}

void MainHead::createUI()
{
	m_logo = new ActivityLabel(this);
	m_logo->setPicName(QString(":/img/logo"));

	m_sysSet = new ActivityLabel(this);
	m_sysSet->setPicName(QString(":/img/switchButton"));

	m_minBtn = new ActivityLabel(this);
	m_minBtn->setPicName(QString(":/img/minButton"));

	m_maxBtn = new ActivityLabel(this);
	m_maxBtn->setPicName(QString(":/img/maxButton"));

	m_closeBtn = new ActivityLabel(this);
	m_closeBtn->setPicName(QString(":/img/closeButton"));

	this->setFixedHeight(50);
}

void MainHead::setLayoutUI()
{
	QHBoxLayout* mainLayout = new QHBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_logo);
	mainLayout->addStretch();
	mainLayout->addWidget(m_sysSet);
	mainLayout->addWidget(m_minBtn);
	mainLayout->addWidget(m_maxBtn);
	mainLayout->addWidget(m_closeBtn);
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(20, 0, 0, 0);

	this->setLayout(mainLayout);
}

void MainHead::setConnection()
{
	connect(m_minBtn, SIGNAL(Lclicked()), this, SIGNAL(showMin()));
	connect(m_maxBtn, SIGNAL(Lclicked()), this, SIGNAL(showMax()));
	connect(m_closeBtn, SIGNAL(Lclicked()), this, SIGNAL(closeWidget()));
}

void MainHead::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(22, 190, 176));
}

void MainHead::mousePressEvent(QMouseEvent *event)
{
	if (event->button() == Qt::LeftButton)
	{
		mouse_press = true;
	}
	MainWidget *pMainWidget = (qobject_cast<MainWidget *>(parent()));

	move_point = event->globalPos() - pMainWidget->pos();
}

void MainHead::mouseReleaseEvent(QMouseEvent *)
{
	mouse_press = false;
}

void MainHead::mouseMoveEvent(QMouseEvent *event)
{
	if (mouse_press)
	{
		QPoint move_pos = event->globalPos();
		MainWidget *pMainWidget = (qobject_cast<MainWidget *>(parent()));
		pMainWidget->move(move_pos - move_point);
	}
}
