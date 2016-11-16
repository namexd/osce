#include "watchmanager.h"
#include <QPainter>
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QFormLayout>
#include <QListView>

WatchManagerTopWidget::WatchManagerTopWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

WatchManagerTopWidget::~WatchManagerTopWidget()
{

}

void WatchManagerTopWidget::createUI()
{
	m_topborder = new QWidget(this);
	m_topborder->setFixedHeight(3);
	m_topborder->setStyleSheet("border:0px;background-color:rgb(22,190,176);");

	m_pic = new ActivityLabel(this);
	m_pic->setPicName(QString(":/img/back"));

	m_bottom = new QWidget(this);
	m_bottom->setFixedHeight(1);
	m_bottom->setStyleSheet("border:0px;background-color:rgb(227,227,227);");

	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft.setBold(true);
	m_name = new QLabel(this);
	m_name->setFixedSize(100, 30);
	m_name->setText(QStringLiteral("Íó±íÉè±¸¹ÜÀí"));
	m_name->setFont(ft);
	m_name->setStyleSheet("color:rgb(22,190,176);");



	this->setFixedHeight(50);
}

void WatchManagerTopWidget::setLayoutUI()
{
	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addWidget(m_name);
	hLayout->addStretch();
	hLayout->addWidget(m_pic);
	hLayout->setContentsMargins(20, 0, 30, 0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->setSpacing(0);
	mainLayout->addWidget(m_topborder);
	mainLayout->addStretch();
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	mainLayout->addWidget(m_bottom);
	mainLayout->addStretch();

	mainLayout->setContentsMargins(0, 0, 0, 0);
	this->setLayout(mainLayout);
}

void WatchManagerTopWidget::setConnection()
{
	connect(m_pic, SIGNAL(Lclicked()), this, SLOT(setMainContentPage()));
}

void WatchManagerTopWidget::hideBack()
{
	m_pic->hide();
}

void WatchManagerTopWidget::setMainContentPage()
{
	int page = 0;
	emit turnMainContentPage(page);
}

//-------------------------------------------------------------------------------------------
WatchContentSearch::WatchContentSearch(QWidget* parent) :QWidget(parent)
{
	index = -1;
	createUI();
	setLayoutUI();
	setConnection();
}

WatchContentSearch::~WatchContentSearch()
{

}

void WatchContentSearch::createUI()
{
	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));

	m_deviceInputID = new QLineEdit(this);
	m_deviceInputID->setFixedSize(240,30);
	m_deviceInputID->setPlaceholderText(QStringLiteral("Éè±¸ID"));
	m_deviceInputID->setFont(ft);
	m_deviceInputID->setTextMargins(10, 0, 0, 0);
	m_deviceInputID->setStyleSheet("border:1px solid rgb(229,230,231);color:rgb(184,184,184);");
	m_deviceInputID->setContextMenuPolicy(Qt::NoContextMenu);

	QStringList items;
	items << QStringLiteral("ÇëÑ¡ÔñÉè±¸×´Ì¬") << QStringLiteral("Î´Ê¹ÓÃ") << QStringLiteral("Ê¹ÓÃÖÐ") << QStringLiteral("Î¬ÐÞ") << QStringLiteral("±¨·Ï");
	m_statSelect = new QComboBox(this);
	m_statSelect->setFixedSize(240,30);
	m_statSelect->addItems(items);
	m_statSelect->setContextMenuPolicy(Qt::NoContextMenu);

	m_statSelect->setView(new QListView());
	m_statSelect->setEditable(true);
	//m_statSelect->setLineEdit(new QTComboBoxButton(m_pAnswer));
	m_statSelect->lineEdit()->setReadOnly(true);
	m_statSelect->setMaxVisibleItems(m_statSelect->count());
	//m_statSelect->lineEdit()->setText(QStringLiteral("ÇëÑ¡ÔñÉè±¸×´Ì¬"));
	m_statSelect->lineEdit()->setStyleSheet("color:rgb(184,184,184);");
	m_statSelect->lineEdit()->setFont(ft);
	m_statSelect->lineEdit()->setTextMargins(10, 0, 0, 0);

	m_statSelect->view()->setStyleSheet("QListView {font-family: \"Î¢ÈíÑÅºÚ\"; font-size: 12px; outline: 0px;}"
		"QListView::item {padding: 3px 0x 3px 5px; border-width: 2px;}"
		"QListView::item:selected {background-color: rgb(74, 144, 226);}");
	//m_statSelect->setCurrentIndex(-1);

	m_search = new ActivityLabel(this);
	m_search->setPicName(QString(":/img/search"));
	this->setFixedHeight(66);
}

void WatchContentSearch::setLayoutUI()
{
	

	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addWidget(m_deviceInputID);
	hLayout->addWidget(m_statSelect);
	hLayout->addWidget(m_search);
	hLayout->setSpacing(10);
	hLayout->addStretch();
	hLayout->setContentsMargins(25, 20, 0, 0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	mainLayout->setContentsMargins(0,0,0,0);
	this->setLayout(mainLayout);

}

void WatchContentSearch::setConnection()
{
	connect(m_statSelect, SIGNAL(currentIndexChanged(int)), this, SLOT(setIndex(int)));
	connect(m_search, SIGNAL(Lclicked()), this, SLOT(dealSearch()));
}

void WatchContentSearch::dealSearch()
{
	QString id = m_deviceInputID->text();
	QString stat = QString::number(index,10);

	emit search(id,stat);
}

void WatchContentSearch::setIndex(int data)
{
	if (data <= 0)
	{
		index = -1;
	}
	index = data-1;
}

void WatchContentSearch::init()
{
	index = -1;
	m_deviceInputID->setText(QStringLiteral(""));
	m_statSelect->setCurrentIndex(index+1);
}

//--------------------------------------------------
WatchManagerContentWidget::WatchManagerContentWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

WatchManagerContentWidget::~WatchManagerContentWidget()
{

}

void WatchManagerContentWidget::createUI()
{
	m_search = new WatchContentSearch(this);
	m_view = new WatchView(this);
	m_page = new PagingWidget(this);
	m_page->setWatchView(m_view);
}

void WatchManagerContentWidget::setLayoutUI()
{
	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addStretch();
	hLayout->addWidget(m_view);
	hLayout->addStretch();
	hLayout->setContentsMargins(10,0,0,0);

	QHBoxLayout* hPageLayout = new QHBoxLayout();
	hPageLayout->setMargin(0);
	hPageLayout->addStretch();
	hPageLayout->addWidget(m_page);
	hPageLayout->addStretch();
	hPageLayout->setContentsMargins(10, 0, 0, 0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_search);
	mainLayout->addLayout(hLayout);
	mainLayout->addLayout(hPageLayout);
	mainLayout->addStretch();
	mainLayout->setContentsMargins(0,0,0,0);

	this->setLayout(mainLayout);
}

void WatchManagerContentWidget::setConnection()
{
	connect(m_view, SIGNAL(loadPage()), m_page, SLOT(restPage()));
	connect(m_search, SIGNAL(search(QString, QString)), m_view, SLOT(search(QString, QString)));
}

void WatchManagerContentWidget::SigAddWatch(QString data)
{
	m_view->popAdd(data);
}


void WatchManagerContentWidget::LoadData()
{
	m_view->LoadData();
}

void WatchManagerContentWidget::initSearch()
{
	m_search->init();
}
//--------------------------------------------------
WatchManagerWidget::WatchManagerWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

WatchManagerWidget::~WatchManagerWidget()
{

}

void WatchManagerWidget::createUI()
{
	m_top = new WatchManagerTopWidget(this);
	m_content = new WatchManagerContentWidget(this);

	this->setFixedSize(1000,514);
}

void WatchManagerWidget::setLayoutUI()
{
	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_top);
	mainLayout->addWidget(m_content);
	mainLayout->addStretch();
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(0, 0, 0, 0);
	this->setLayout(mainLayout);
}

void WatchManagerWidget::setConnection()
{
	connect(m_top, SIGNAL(turnMainContentPage(int)), this, SIGNAL(turnMainContentPage(int)));
}

void WatchManagerWidget::SigAddWatch(QString data)
{
	m_content->SigAddWatch(data);
}

void WatchManagerWidget::LoadData()
{
	m_content->LoadData();
}

void WatchManagerWidget::initSearch()
{
	m_content->initSearch();
}

void WatchManagerWidget::setTop()
{
	m_top->hideBack();
}

void WatchManagerWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(255, 255, 255));
}

//--------------------------------------------------
WatchManager::WatchManager(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

WatchManager::~WatchManager()
{

}

void WatchManager::createUI()
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

	m_tip = new QLabel(this);
	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft.setBold(true);

	m_tip->setStyleSheet("color:rgb(103,106,108);");
	m_tip->setFixedSize(200, 30);
	m_tip->setFont(ft);
	m_tip->setAlignment(Qt::AlignCenter);

	m_content = new WatchManagerWidget(this);
}

void WatchManager::setLayoutUI()
{
	/*QHBoxLayout* hBindTip = new QHBoxLayout();
	hBindTip->setMargin(0);
	hBindTip->addStretch();
	hBindTip->addWidget(m_stackedWidget);
	hBindTip->addStretch();
	hBindTip->setContentsMargins(0, 50, 0, 0);*/

	QHBoxLayout* hTip = new QHBoxLayout();
	hTip->setMargin(0);
	hTip->addStretch();
	hTip->addWidget(m_tip);
	hTip->addStretch();
	hTip->setContentsMargins(0, 20, 0, 0);

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
	mainLayout->addLayout(hTip);
	mainLayout->addStretch();
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	mainLayout->setSpacing(0);
	mainLayout->addStretch();
	mainLayout->setContentsMargins(0, 0, 0, 0);
	this->setLayout(mainLayout);
}

void WatchManager::setConnection()
{
	connect(m_content, SIGNAL(turnMainContentPage(int)), this, SIGNAL(turnMainContentPage(int)));
}

void WatchManager::SigAddWatch(QString data)
{
	m_content->SigAddWatch(data);
}

void WatchManager::LoadData()
{
	m_content->LoadData();
}

void WatchManager::initSearch()
{
	m_content->initSearch();
}

void WatchManager::slotNoExam(QString name)
{
	m_tip->setText(name);
	m_content->setTop();
}

void WatchManager::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(243, 243, 243));
}