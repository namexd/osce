#include "maincontent.h"
#include <QPainter>
#include <QVBoxLayout>
#include <QHBoxLayout>
MainContent::MainContent(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

MainContent::~MainContent()
{

}

void MainContent::createUI()
{
	m_bind = new BindWidget(this);
	m_watchManager = new WatchManager(this);
	m_login = new LoginWidget(this);
	m_selectexam = new SelectExamWidget(this);
	m_stackedWidget = new QStackedWidget(this);

	m_bind->StopTime();

	QPalette palette;
	palette.setBrush(QPalette::Window, QBrush(Qt::white));
	m_stackedWidget->setPalette(palette);
	m_stackedWidget->setAutoFillBackground(true);
	m_stackedWidget->setSizePolicy(QSizePolicy::Expanding, QSizePolicy::Expanding);

	m_stackedWidget->addWidget(m_bind);
	m_stackedWidget->addWidget(m_watchManager);
	m_stackedWidget->addWidget(m_login);
	m_stackedWidget->addWidget(m_selectexam);

	m_stackedWidget->setCurrentWidget(m_login);

}

void MainContent::setLayoutUI()
{
	QVBoxLayout *mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addSpacing(0);
	mainLayout->addWidget(m_stackedWidget);

	mainLayout->setContentsMargins(0, 0, 0, 0);
	this->setLayout(mainLayout);
}

void MainContent::setConnection()
{
	connect(m_login, SIGNAL(setExamViewData(QStringList, QStringList)), m_selectexam,SLOT(setExamViewData(QStringList, QStringList)));
	connect(m_bind, SIGNAL(turnMainContentPage(int)), this, SLOT(turnPage(int)));
	connect(m_login, SIGNAL(turnMainContentPage(int)), this, SLOT(turnPage(int)));
	connect(m_selectexam, SIGNAL(turnMainContentPage(int)), this, SLOT(turnPage(int)));
	//connect(m_bind, SIGNAL(turnMainContentPage(int)), this, SLOT(turnPage(int)));
	connect(m_watchManager, SIGNAL(turnMainContentPage(int)), this, SLOT(turnPage(int)));
	connect(m_bind, SIGNAL(SigAddWatch(QString)), m_watchManager, SLOT(SigAddWatch(QString)));
	connect(m_login, SIGNAL(sigNoExam(QString)), m_watchManager, SLOT(slotNoExam(QString)));
}

void MainContent::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(243, 243, 243));
}

void MainContent::turnPage(int curIndex)
{
	switch (curIndex)
	{
		case Stacked_Bind:
		{
							 m_stackedWidget->setCurrentWidget(m_bind);
							 m_bind->StartTime();
				break;
		}

		case Stacked_WatchManager:
		{
									 m_watchManager->initSearch();
									 m_stackedWidget->setCurrentWidget(m_watchManager);
									 m_bind->StopTime();
									 m_watchManager->LoadData();
				break;
		}
		case Stacked_Login:
		{
									 m_stackedWidget->setCurrentWidget(m_login);
									 m_bind->StopTime();
									 break;
		}
		case Stacked_Exam:
		{
									 m_stackedWidget->setCurrentWidget(m_selectexam);
									 m_bind->StopTime();
									 break;
		}
		default:
			break;
	}
}