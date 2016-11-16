#include "mainwidget.h"
#include <QVBoxLayout>

MainWidget::MainWidget(QWidget* parent) :DropShadowWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

MainWidget::~MainWidget()
{

}

void MainWidget::createUI()
{
	m_head = new MainHead(this);
	m_content = new MainContent(this);

	this->setWindowState(Qt::WindowNoState);
	this->setMinimumSize(1024, 768);
}

void MainWidget::setLayoutUI()
{
	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_head);
	mainLayout->addWidget(m_content);
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(0, 0, 0, 0);
	this->setLayout(mainLayout);
}

void MainWidget::setConnection()
{
	connect(m_head, SIGNAL(showMin()), this, SLOT(showMinimized()));
	connect(m_head, SIGNAL(showMax()), this, SLOT(widgetSizeSwitch()));
	connect(m_head, SIGNAL(closeWidget()), this, SLOT(close()));
}

void MainWidget::widgetSizeSwitch()
{
	if (this->windowState() == Qt::WindowMaximized)
		this->showNormal();
	else
		this->setWindowState(Qt::WindowMaximized);
}
