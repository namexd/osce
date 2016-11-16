#include "pagingwidget.h"
#include <QHBoxLayout>
#include <QVBoxLayout>

PagingWidget::PagingWidget(QWidget* parent) :QWidget(parent)
{
	indexClick = -1;
	totalPage = -1;
	perPage = 7;
	curentPage = 1;
	bool isSetPicPre = false;//先前翻不可点
	bool isSetPicBak = false;//向后翻不可点
	createUI();
	setLayoutUI();
	setConnection();
}

PagingWidget::~PagingWidget()
{

}

void PagingWidget::createUI()
{
	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("微软雅黑"));
	ft.setBold(true);

	this->setFixedSize(960, 50);

	signalMapperClick = new QSignalMapper(this);
	signalMapperHover = new QSignalMapper(this);
	signalMapperLeave = new QSignalMapper(this);

	m_totalPage = new QLabel(this);
	m_totalPage->setFixedSize(100, 39);
	m_totalPage->setFont(ft);
	m_totalPage->setStyleSheet("color:rgb(153,153,153);");

	m_prepage = new ActivityLabel(this);
	m_prepage->setPicName(QString(":img/pageleft"));

	m_bakpage = new ActivityLabel(this);
	m_bakpage->setPicName(QString(":img/pageright"));


	for (int i = 0; i < 5; i++)
	{
		Label* btn = new Label(this);
		btn->setFixedSize(34,37);
		btn->setFont(ft);
		btn->setAlignment(Qt::AlignCenter);
		if (i == 0)
		{
			btn->setStyleSheet("background:#fff;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
		}
		else
		{
			btn->setStyleSheet("background:#fff;color:rgb(153,153,153);border-left:0px;border-top:1px solid rgb(221,221,221);border-right:1px solid rgb(221,221,221);border-bottom:1px solid rgb(221,221,221);");
		}
		
		btn->setText(QString::number(i+1,10));
		m_buttons.append(btn);
		connect(btn, SIGNAL(hover()), signalMapperHover,SLOT(map()));
		connect(btn, SIGNAL(press()), signalMapperClick, SLOT(map()));
		connect(btn, SIGNAL(leave()), signalMapperLeave, SLOT(map()));

		signalMapperHover->setMapping(btn, QString::number(i, 10));
		signalMapperClick->setMapping(btn, QString::number(i, 10));
		signalMapperLeave->setMapping(btn, QString::number(i, 10));
	}	
}

void PagingWidget::setLayoutUI()
{
	QHBoxLayout *hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addWidget(m_totalPage);
	hLayout->addStretch();
	hLayout->addWidget(m_prepage);
	for (int i = 0; i < m_buttons.size(); i++)
	{
		hLayout->addWidget(m_buttons.at(i));
	}
	hLayout->addWidget(m_bakpage);
	hLayout->setSpacing(0);
	hLayout->setContentsMargins(0, 0, 0, 0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addStretch();
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	mainLayout->setContentsMargins(0,0,0,0);
	this->setLayout(mainLayout);
}

void PagingWidget::setConnection()
{
	connect(signalMapperHover, SIGNAL(mapped(QString)), this, SLOT(pageBtnHover(QString)));
	connect(signalMapperClick, SIGNAL(mapped(QString)), this, SLOT(pageBtnClick(QString)));
	connect(signalMapperLeave, SIGNAL(mapped(QString)), this, SLOT(pageBtnLeave(QString)));
	connect(m_bakpage, SIGNAL(Lclicked()), this, SLOT(changeNextPage()));
	connect(m_prepage, SIGNAL(Lclicked()), this, SLOT(changePrePage()));
}

void PagingWidget::pageBtnClick(QString curPage)
{
	bool ok;
	int index = curPage.toInt(&ok, 10);
	//前翻  后翻

	//----
	if (index == 4 && (curentPage+1) != totalPage)
	{
		Label *btn = m_buttons.at(4);
		indexClick = 0;
		curentPage = btn->text().toInt();
		clickButtonChangePreBak(curentPage);
		if (totalPage - curentPage < 4)
		{
			for (int k = 0; k < m_buttons.count(); k++)
			{
				Label *btntemp = m_buttons.at(k);
				if (k <= totalPage - curentPage)
				{
					btntemp->setText(QString::number(curentPage + k * 1, 10));
					changeHoverButton(k, indexClick);
					/*if (k == 0)
					{
						btntemp->setStyleSheet("background:#f4f4f4;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
					}
					else
					{
						btntemp->setStyleSheet("background:#fff;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
						changeHoverButton(0, 0);
					}*/
				}
				else
				{
					btntemp->hide();
				}
			}
			return;
		}
		for (int k = 0; k < m_buttons.count(); k++)
		{
			Label *btntemp = m_buttons.at(k);
			btntemp->setText(QString::number(curentPage + k * 1, 10));
			changeHoverButton(k, indexClick);
			/*if (k == 0)
			{
				btntemp->setStyleSheet("background:#f4f4f4;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
			}
			else
			{
				btntemp->setStyleSheet("background:#fff;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
			}*/
		}
		changePage();
		return;
	}

	for (int i = 0; i<m_buttons.count(); i++)
	{
		Label *btn = m_buttons.at(i);
		if (index == i)
		{
			indexClick = i;
			curentPage = btn->text().toInt();
			//btn->setStyleSheet("background:#f4f4f4;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
			changeHoverButton(i, index);
			changePage();
			clickButtonChangePreBak(curentPage);
		}
		else
		{
			//btn->setStyleSheet("background:#fff;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
			changeHoverButton(i, index);
		}
	}
}

void PagingWidget::pageBtnHover(QString curPage)
{
	bool ok;
	int index = curPage.toInt(&ok, 10);

	for (int i = 0; i<m_buttons.count(); i++)
	{
		Label *btn = m_buttons.at(i);
		if (index == i)
		{
			changeHoverButton(i, index);
			//btn->setStyleSheet("background:#f4f4f4;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
		}
		else if (i != indexClick)
		{
			changeHoverButton(i, index);
			//btn->setStyleSheet("background:#fff;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
		}
	}
}

void PagingWidget::pageBtnLeave(QString curPage)
{
	bool ok;
	int index = curPage.toInt(&ok, 10);

	for (int i = 0; i<m_buttons.count(); i++)
	{
		Label *btn = m_buttons.at(i);
		if (i != indexClick)
		{
			changeHoverButton(i, indexClick);
			//btn->setStyleSheet("background:#fff;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
		}
	}
}


void PagingWidget::setWatchView(WatchView* view)
{
	m_view = view;
	restPage();
	//clickButtonChangePreBak(1);
}

void PagingWidget::setStartButton()
{
	if (totalPage == 1 || totalPage == 0)
	{
		m_prepage->hide();
		m_bakpage->hide();
		for (int i = 0; i<m_buttons.count(); i++)
		{
			Label *btn = m_buttons.at(i);
			btn->hide();
		}
		return;
	}

	pageBtnClick(QString::number(0, 10));
	if (totalPage < 5)
	{
		if (m_prepage->isHidden())
			m_prepage->show();
		if (m_bakpage->isHidden())
			m_bakpage->show();
		for (int i = 0; i<m_buttons.count(); i++)
		{
			Label *btn = m_buttons.at(i);
			if (i < totalPage)
			{
				if (btn->isHidden())
					btn->show();
			}
			else
			{
				btn->hide();
			}
		}
		return;
	}

	if (totalPage >= 5)
	{
		if (m_prepage->isHidden())
			m_prepage->show();
		if (m_bakpage->isHidden())
			m_bakpage->show();
		for (int i = 0; i<m_buttons.count(); i++)
		{
			Label *btn = m_buttons.at(i);
			btn->setText(QString::number(i+1,10));
			if (btn->isHidden())
				btn->show();
		}
		curentPage = 1;
		//curentPage++;
		//changePrePage();
		return;
	}
}

void PagingWidget::changePage()
{
	int rowCount = m_view->getRowCount();
	int startRow = ((curentPage-1) * 7);
	int stopRow = (curentPage * 7) -1;

	for (int i = 0; i < rowCount; i++)
	{
		if (i >= startRow && i <= stopRow)
		{
			m_view->setRowHidden(i,false);
		}
		else
		{
			m_view->setRowHidden(i, true);
		}
	}
}

void PagingWidget::changeNextPage()
{
	if (curentPage == 1 && isSetPicPre)
	{
		isSetPicPre = false;
		m_prepage->setPicName(QString(":img/pageleft"));
	}
	if (curentPage == totalPage)
	{
		isSetPicBak = true;
		m_bakpage->setPicName(QString(":img/noclick_pageright"));
		//m_bakpage->setDisabled(true);
		return;
	}
	//m_bakpage->setDisabled(false);
	if (indexClick < 4)
	{	
		if (isSetPicBak)
		{
			isSetPicBak = false;
			m_bakpage->setPicName(QString(":img/pageright"));
		}
		if ((curentPage + 1) == totalPage)
		{
			isSetPicBak = true;
			m_bakpage->setPicName(QString(":img/noclick_pageright"));
		}
		pageBtnClick(QString::number(indexClick+1,10));
	}
}

void PagingWidget::changePrePage()
{
	if (curentPage == 1)
	{
		isSetPicPre = true;
		m_prepage->setPicName(QString(":img/noclick_pageleft"));
		return;
	}
	if (curentPage == totalPage && isSetPicBak)
	{
		isSetPicBak = false;
		m_bakpage->setPicName(QString(":img/pageright"));
	}
	if (indexClick != 0)
	{
		if (isSetPicPre)
		{
			isSetPicPre = false;
			m_prepage->setPicName(QString(":img/pageleft"));
		}
		if ((curentPage - 1) == 1)
		{
			isSetPicPre = true;
			m_prepage->setPicName(QString(":img/noclick_pageleft"));
		}
		pageBtnClick(QString::number(indexClick - 1, 10));
	}
	else
	{
		if (curentPage == 1)
		{
			isSetPicPre = true;
			m_prepage->setPicName(QString(":img/noclick_pageleft"));
			return;
		}
		if (isSetPicPre)
		{
			isSetPicPre = false;
			m_prepage->setPicName(QString(":img/pageleft"));
		}
		for (int k = 0; k < m_buttons.count(); k++)
		{
			Label *btntemp = m_buttons.at(k);
			btntemp->setText(QString::number(curentPage -(4-k), 10));
			if (btntemp->isHidden())
				btntemp->show();
		}
		pageBtnClick(QString::number(3, 10));
	}
}

void PagingWidget::clickButtonChangePreBak(int cpage)
{
	if (cpage == 1 || cpage == totalPage)
	{
		if (cpage == 1)
		{
			isSetPicPre = true;
			m_prepage->setPicName(QString(":img/noclick_pageleft"));
			if (isSetPicBak)
			{
				isSetPicBak = false;
				m_bakpage->setPicName(QString(":img/pageright"));
			}
		}
		if (cpage == totalPage)
		{
			isSetPicBak = true;
			m_bakpage->setPicName(QString(":img/noclick_pageright"));
			if (isSetPicPre)
			{
				isSetPicPre = false;
				m_prepage->setPicName(QString(":img/pageleft"));
			}
		}
	}
	else
	{
		if (isSetPicBak)
		{
			isSetPicBak = false;
			m_bakpage->setPicName(QString(":img/pageright"));
		}
		if (isSetPicPre)
		{
			isSetPicPre = false;
			m_prepage->setPicName(QString(":img/pageleft"));
		}
	}
}

void PagingWidget::changeHoverButton(int cur, int index)
{
	Label *btn = m_buttons.at(cur);
	if (cur == index)
	{
		if (cur == 0)
		{
			btn->setStyleSheet("background:#f4f4f4;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
		}
		else
		{
			btn->setStyleSheet("background:#f4f4f4;color:rgb(153,153,153);border-left:0px;border-top:1px solid rgb(221,221,221);border-right:1px solid rgb(221,221,221);border-bottom:1px solid rgb(221,221,221);");
		}
	}
	else
	{
		if (cur == 0)
		{
			btn->setStyleSheet("background:#fff;color:rgb(153,153,153);border:1px solid rgb(221,221,221);");
		}
		else
		{
			btn->setStyleSheet("background:#fff;color:rgb(153,153,153);border-left:0px;border-top:1px solid rgb(221,221,221);border-right:1px solid rgb(221,221,221);border-bottom:1px solid rgb(221,221,221);");
		}
	}
}

void PagingWidget::restPage()
{
	int rowCount = m_view->getRowCount();
	if (0 != rowCount%perPage)
	{
		totalPage = (rowCount / perPage) + 1;
	}
	else
	{
		totalPage = (rowCount / perPage);
	}
	QString tip = QStringLiteral("共") + QString::number(totalPage, 10) + QStringLiteral("页");
	m_totalPage->setText(tip);
	setStartButton();
	changePage();
}