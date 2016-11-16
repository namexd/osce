#ifndef CALENDAR
#define CALENDAR

#include <QMenu>
#include <QLabel>
#include <QLineEdit>
#include <QWidgetAction>
#include <QCalendarWidget>
#include "activitylabel.h"

class CalendarItem:public QWidget
{
    Q_OBJECT
public:
    explicit CalendarItem(QWidget *parent = 0);
    ~CalendarItem();
    void setYear(int year);
signals:
    void retDate(QString date);
    void closeWidget();
public slots:
    void addYear();
    void subYear();
    void addMonth();
    void subMonth();
    void addDay();
    void subDay();
    void setRet();
    void setTodayTitle();
private:
    void createUI();
    void setLayoutUI();
    void setConnection();
    QString transferData(int data);
    int QStringToInt(QString data);
    int getMaxDay();
    void getchangeDate();

    QCalendarWidget* caledar;

    QLabel* m_title;

    QLabel* m_yearflag;
    ActivityLabel* m_yearadd;
    QLabel* m_year;
    ActivityLabel* m_yearsub;

    QLabel* m_monthflag;
    ActivityLabel* m_monthadd;
    QLabel* m_month;
    ActivityLabel* m_monthsub;


    QLabel* m_dayflag;
    ActivityLabel* m_dayadd;
    QLabel* m_day;
    ActivityLabel* m_daysub;

    ActivityLabel* m_confirm;
    ActivityLabel* m_today;
    ActivityLabel* m_cancel;

    int year;
    int month;
    int day;
};

class CalendarMenu:public QMenu
{
    Q_OBJECT
public:
    explicit CalendarMenu(QWidget *parent = 0);
    ~CalendarMenu();
    void setYear(int year);
signals:
    void retDate(QString date);
    void closeWidget();
private:
    QWidgetAction* popaction;
    CalendarItem* item;

    void createUI();
    void setLayoutUI();
    void setConnection();
};



class ClickEdit:public QLineEdit
{
    Q_OBJECT
public:
    explicit ClickEdit(QWidget *parent = 0);
    virtual void mousePressEvent(QMouseEvent *event);
signals:
    void clicked(QWidget* widget);
};

#endif // CALENDAR

