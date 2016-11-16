#include "selectexamwidget.h"

#include <QHBoxLayout>
#include <QVBoxLayout>
#include <QPainter>

SelectExamContentWidget::SelectExamContentWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

SelectExamContentWidget::~SelectExamContentWidget()
{

}

void SelectExamContentWidget::createUI()
{
	QFont ft1;
	ft1.setPointSize(12);
	ft1.setFamily(QStringLiteral("Î¢ÈíÑÅºÚ"));
	ft1.setBold(true);

	m_head = new QWidget(this);
	m_head->setFixedHeight(3);
	m_head->setStyleSheet("border:0px;background-color:rgb(22,190,176);");

	m_tag = new QLabel(this);
	m_tag->setFixedSize(280,30);

	m_tag = new QLabel(this);
	m_tag->setFixedSize(280, 30);
	m_tag->setText(QStringLiteral("ÇëÑ¡Ôñµ±Ç°¿¼ÊÔ"));
	m_tag->setStyleSheet("color:rgb(103,106,108);");
	m_tag->setAlignment(Qt::AlignCenter);
	m_tag->setFont(ft1);

	m_view = new ExamView(this);

	this->setFixedSize(360, 440);
}

void SelectExamContentWidget::setLayoutUI()
{
	QVBoxLayout* h1 = new QVBoxLayout();
	h1->setMargin(0);
	h1->addWidget(m_tag);
	h1->addWidget(m_view);
	h1->setSpacing(0);
	h1->setContentsMargins(0,0,0,0);

	QHBoxLayout* hContent = new QHBoxLayout();
	hContent->setMargin(0);
	hContent->addStretch();
	hContent->addLayout(h1);
	hContent->addStretch();
	hContent->setContentsMargins(0,0,0,0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_head);
	mainLayout->addStretch();
	mainLayout->addLayout(hContent);
	mainLayout->addStretch();
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(0,0,0,0);

	this->setLayout(mainLayout);
}

void SelectExamContentWidget::setConnection()
{
	connect(m_view, SIGNAL(turnMainContentPage(int)), this, SIGNAL(turnMainContentPage(int)));
}

void SelectExamContentWidget::setExamViewData(QStringList ids, QStringList names)
{
	m_view->setExamViewData(ids,names);
}

void SelectExamContentWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(255, 255, 255));
}

//----------------------------------
SelectExamWidget::SelectExamWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

SelectExamWidget::~SelectExamWidget()
{

}

void SelectExamWidget::createUI()
{
	m_content = new SelectExamContentWidget(this);
}

void SelectExamWidget::setLayoutUI()
{
	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addStretch();
	hLayout->addWidget(m_content);
	hLayout->addStretch();
	hLayout->setContentsMargins(0, 0, 0, 0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addStretch();
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	hLayout->setContentsMargins(0, 0, 0, 0);

	this->setLayout(mainLayout);
}

void SelectExamWidget::setConnection()
{
	connect(m_content, SIGNAL(turnMainContentPage(int)), this, SIGNAL(turnMainContentPage(int)));
}

void SelectExamWidget::setExamViewData(QStringList ids, QStringList names)
{
	m_content->setExamViewData(ids,names);
}

void SelectExamWidget::paintEvent(QPaintEvent *)
{
	QPainter painter(this);
	painter.setPen(Qt::NoPen);
	painter.setBrush(Qt::white);
	painter.fillRect(this->rect(), QColor(243, 243, 243));
}