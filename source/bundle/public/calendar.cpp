#include "calendar.h"
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QDateTime>

CalendarItem::CalendarItem(QWidget *parent)
    :QWidget(parent)
{
    year = 2015;
    month = 9;
    day = 10;
    createUI();
    setLayoutUI();
    setConnection();
    setTodayTitle();
}

CalendarItem::~CalendarItem()
{

}

void CalendarItem::createUI()
{
	QFont ft;
	ft.setPointSize(10);
    ft.setFamily(QStringLiteral("微软雅黑"));

    QFont f;
    f.setPointSize(12);
	f.setFamily(QStringLiteral("微软雅黑"));

    m_title = new QLabel();
    m_title->setStyleSheet("background:rgb(22,190,176);color:white;");
    m_title->setFixedHeight(32);
    m_title->setAlignment(Qt::AlignCenter);
    m_title->setFont(f);


    m_yearflag = new QLabel();
    m_yearflag->setFixedWidth(73);
	m_yearflag->setText(QStringLiteral("年"));
	m_yearflag->setFont(ft);
    m_yearflag->setAlignment(Qt::AlignCenter);
    m_yearadd = new ActivityLabel();
    m_yearadd->setPicName(":/img/dateadd");
    m_year= new QLabel();
    m_year->setFont(f);
    m_year->setFixedSize(73,43);
    m_year->setAlignment(Qt::AlignCenter);
    m_year->setStyleSheet("border-left:1px solid rgb(183,183,183);border-right:1px solid rgb(183,183,183);border-top:0px;border-bottom:0px;");
    m_yearsub = new ActivityLabel();
    m_yearsub->setPicName(":/img/datesub");


    m_monthflag = new QLabel();
    m_monthflag->setFixedWidth(73);
	m_monthflag->setText(QStringLiteral("月"));
	m_monthflag->setFont(ft);
    m_monthflag->setAlignment(Qt::AlignCenter);
    m_monthadd = new ActivityLabel();
    m_monthadd->setPicName(":/img/dateadd");
    m_month= new QLabel();
    m_month->setFont(f);
    m_month->setFixedSize(73,43);
    m_month->setAlignment(Qt::AlignCenter);
    m_month->setStyleSheet("border-left:1px solid rgb(183,183,183);border-right:1px solid rgb(183,183,183);border-top:0px;border-bottom:0px;");
    m_monthsub = new ActivityLabel();
    m_monthsub->setPicName(":/img/datesub");


    m_dayflag = new QLabel();
    m_dayflag->setFixedWidth(73);
	m_dayflag->setText(QStringLiteral("日"));
    m_dayflag->setFont(ft);
    m_dayflag->setAlignment(Qt::AlignCenter);
    m_dayadd = new ActivityLabel();
    m_dayadd->setPicName(":/img/dateadd");
    m_day= new QLabel();
    m_day->setFont(f);
    m_day->setFixedSize(73,43);
    m_day->setAlignment(Qt::AlignCenter);
    m_day->setStyleSheet("border-left:1px solid rgb(183,183,183);border-right:1px solid rgb(183,183,183);border-top:0px;border-bottom:0px;");
    m_daysub = new ActivityLabel();
    m_daysub->setPicName(":/img/datesub");

    m_confirm = new ActivityLabel();
    m_confirm->setPicName(":/img/dateconfirm");

    m_today = new ActivityLabel();
    m_today->setPicName(":/img/datetoday");
    m_cancel = new ActivityLabel();
    m_cancel->setPicName(":/img/datecancel");

    m_year->setText(transferData(year));
    m_month->setText(transferData(month));
    m_day->setText(transferData(day));
}

void CalendarItem::setLayoutUI()
{
    QVBoxLayout* yearLayout = new QVBoxLayout();
    yearLayout->addWidget(m_yearflag);
    yearLayout->addWidget(m_yearadd);
    yearLayout->addWidget(m_year);
    yearLayout->addWidget(m_yearsub);
    yearLayout->setMargin(0);
    yearLayout->setSpacing(0);
    yearLayout->setContentsMargins(0,0,0,0);

    QVBoxLayout* monthLayout = new QVBoxLayout();
    monthLayout->addWidget(m_monthflag);
    monthLayout->addWidget(m_monthadd);
    monthLayout->addWidget(m_month);
    monthLayout->addWidget(m_monthsub);
    monthLayout->setMargin(0);
    monthLayout->setSpacing(0);
    monthLayout->setContentsMargins(0,0,0,0);

    QVBoxLayout* dayLayout = new QVBoxLayout();
    dayLayout->addWidget(m_dayflag);
    dayLayout->addWidget(m_dayadd);
    dayLayout->addWidget(m_day);
    dayLayout->addWidget(m_daysub);
    dayLayout->setMargin(0);
    dayLayout->setSpacing(0);
    dayLayout->setContentsMargins(0,0,0,0);

    QHBoxLayout* hLayout = new QHBoxLayout();
    hLayout->addStretch();
    hLayout->addLayout(yearLayout);
    hLayout->addSpacing(5);
    hLayout->addLayout(monthLayout);
    hLayout->addSpacing(5);
    hLayout->addLayout(dayLayout);
    hLayout->addStretch();
    hLayout->setMargin(0);
    hLayout->setContentsMargins(0,0,0,0);

    QHBoxLayout* hButton = new QHBoxLayout();
    hButton->addStretch();
    hButton->addWidget(m_confirm);
    hButton->addStretch();
    hButton->addWidget(m_today);
    hButton->addStretch();
    hButton->addWidget(m_cancel);
    hButton->addStretch();
    hButton->setContentsMargins(0,0,0,0);



    QVBoxLayout* mainLayout = new QVBoxLayout();
    mainLayout->addWidget(m_title);
    mainLayout->addStretch();
    mainLayout->addLayout(hLayout);
    mainLayout->addStretch();
    mainLayout->addLayout(hButton);
    mainLayout->setMargin(0);
    mainLayout->setSpacing(0);
    mainLayout->setContentsMargins(0,0,1,10);

    this->setLayout(mainLayout);
}

void CalendarItem::setConnection()
{
    connect(m_yearadd,SIGNAL(Lclicked()),this,SLOT(addYear()));
    connect(m_yearsub,SIGNAL(Lclicked()),this,SLOT(subYear()));
    connect(m_monthadd,SIGNAL(Lclicked()),this,SLOT(addMonth()));
    connect(m_monthsub,SIGNAL(Lclicked()),this,SLOT(subMonth()));
    connect(m_dayadd,SIGNAL(Lclicked()),this,SLOT(addDay()));
    connect(m_daysub,SIGNAL(Lclicked()),this,SLOT(subDay()));
    connect(m_confirm,SIGNAL(Lclicked()),this,SLOT(setRet()));
    connect(m_today,SIGNAL(Lclicked()),this,SLOT(setTodayTitle()));
    connect(m_cancel,SIGNAL(Lclicked()),this,SIGNAL(closeWidget()));
}

QString CalendarItem::transferData(int data)
{
    QString ret;
    ret.clear();
    if(data/10 == 0)
    {
        ret = QString("0")+QString::number(data);
    }
    else
    {
        ret = QString::number(data);
    }

   return ret;
}

int CalendarItem::QStringToInt(QString data)
{
    bool ok;
    int dec=data.toInt(&ok,10);
    return dec;
}

int CalendarItem::getMaxDay()
{
    int day=0;
    int textyear = QStringToInt(m_year->text());
    int textmonth = QStringToInt(m_month->text());
    if(textmonth == 2)
    {
        if((textyear%4 == 0 && textyear%100 != 0) || (textyear % 400 == 0))
        {
            day = 29;
        }
        else
        {
            day = 28;
        }
        return day;
    }

    int maxday = 31;
    int midday = 30;

    switch (textmonth)
    {
    case 1:
        day = maxday;
        break;
    case 2:

        break;
    case 3:
        day = maxday;
        break;
    case 4:
        day = midday;
        break;
    case 5:
        day = maxday;
        break;
    case 6:
        day = midday;
        break;
    case 7:
        day = maxday;
        break;
    case 8:
        day = maxday;
        break;
    case 9:
        day = midday;
        break;
    case 10:
        day = maxday;
        break;
    case 11:
        day = midday;
        break;
    case 12:
        day = maxday;
        break;
    default:
        break;
    }
    return day;
}

void CalendarItem::addYear()
{
    int textyear = QStringToInt(m_year->text());
    if(textyear>=2020)
        return;
    m_year->setText(transferData(textyear+1));

    int textmonth = QStringToInt(m_month->text());
    if(textmonth != 2)
    {
        getchangeDate();
        return;
    }
    int textday = QStringToInt(m_day->text());
    if(textday>getMaxDay())
        m_day->setText(transferData(1));
    getchangeDate();
}

void CalendarItem::subYear()
{
    int textyear = QStringToInt(m_year->text());
    if(textyear<=year)
        return;
    m_year->setText(transferData(textyear-1));

    int textmonth = QStringToInt(m_month->text());
    if(textmonth != 2)
    {
        getchangeDate();
        return;
    }
    int textday = QStringToInt(m_day->text());
    if(textday>getMaxDay())
        m_day->setText(transferData(1));
    getchangeDate();
}

void CalendarItem::addMonth()
{
    int textmonth = QStringToInt(m_month->text());
    if(textmonth>=12)
        return;
    m_month->setText(transferData(textmonth+1));

    int textday = QStringToInt(m_day->text());
    if(textday > getMaxDay())
         m_day->setText(transferData(1));
    getchangeDate();
}

void CalendarItem::subMonth()
{
    int textmonth = QStringToInt(m_month->text());
    if(textmonth<=1)
        return;
    m_month->setText(transferData(textmonth-1));

    int textday = QStringToInt(m_day->text());
    if(textday > getMaxDay())
         m_day->setText(transferData(1));
    getchangeDate();
}

void CalendarItem::addDay()
{
    int textday = QStringToInt(m_day->text());
    if(textday>=getMaxDay())
        return;
    m_day->setText(transferData(textday+1));
    getchangeDate();
}

void CalendarItem::subDay()
{
    int textday = QStringToInt(m_day->text());
    if(textday<=1)
        return;
    m_day->setText(transferData(textday-1));
    getchangeDate();
}

void CalendarItem::getchangeDate()
{
    QString date = m_year->text().trimmed()+QString("-")+m_month->text().trimmed()+QString("-")+m_day->text().trimmed();
    m_title->setText(date);
}

void CalendarItem::setRet()
{
    emit retDate(m_title->text().trimmed());
}

void CalendarItem::setTodayTitle()
{
    QDateTime ctime = QDateTime::currentDateTime();
    QString today = ctime.toString("yyyy-MM-dd");
    m_title->setText(today);
    QStringList strlist = today.split("-");
    m_year->setText(strlist.at(0));
    m_month->setText(strlist.at(1));
    m_day->setText(strlist.at(2));
}

void CalendarItem::setYear(int year)
{
    this->year = year;
}

//--------------------------------------------------
CalendarMenu::CalendarMenu(QWidget* parent)
    :QMenu(parent)
{
    this->setMouseTracking(true);
	this->setFixedWidth(370);
    this->setObjectName("switchmenu");
    createUI();
    setLayoutUI();
    setConnection();
}

CalendarMenu::~CalendarMenu()
{

}

void CalendarMenu::createUI()
{
    popaction = new QWidgetAction(this);
    item = new CalendarItem();
    item->setFixedSize(370,255);

    popaction->setDefaultWidget(item);
    this->addAction(popaction);

    this->setAttribute(Qt::WA_TranslucentBackground, true);
}

void CalendarMenu::setLayoutUI()
{

}

void CalendarMenu::setYear(int year)
{
    item->setYear(year);
}

void CalendarMenu::setConnection()
{
    connect(item,SIGNAL(retDate(QString)),this,SIGNAL(retDate(QString)));
    connect(item,SIGNAL(closeWidget()),this,SIGNAL(closeWidget()));
}


//---------------------------------------------------------------------------------------------
ClickEdit::ClickEdit(QWidget *parent):
    QLineEdit(parent)
{

}
void ClickEdit::mousePressEvent(QMouseEvent *event)
{
    if (event->button() == Qt::LeftButton)
    {
       emit clicked(this);
    }
    QLineEdit::mousePressEvent(event);
}

