#ifndef BINDWIDGET
#define BINDWIDGET

#include <QWidget>
#include <QLabel>
#include <QLineEdit>
#include <QStackedWidget>
#include <QTimer>
#include "../../public/activitylabel.h"
#include "../../public/dropShadow.h"
#include "../../public/tip.h"
#include "readcards.h"
//--------------------------------------------------
class CommentBindTopWidget :public QWidget
{
	Q_OBJECT
public:
	explicit CommentBindTopWidget(QWidget* parent = 0);
	~CommentBindTopWidget();

	void setName(QString sname);
protected:
	void paintEvent(QPaintEvent *);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QWidget* m_topborder;
	ActivityLabel* m_pic;
	QLabel* m_name;
	QWidget* m_bottom;
	QString name;
};

//--------------------------------------------------
class StudentInfoWidget :public QWidget
{
	Q_OBJECT
public:
	explicit StudentInfoWidget(QWidget* parent = 0);
	~StudentInfoWidget();
protected:
	void paintEvent(QPaintEvent *);
public slots:
	void setIDCardData(QString stuName, QString stuNO, QString idNO, QString ticketNO);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	CommentBindTopWidget* m_head;

	QLabel* m_name;
	QLineEdit* m_nameEdit;

	QLabel* m_stuNO;
	QLineEdit* m_stuNOEdit;

	QLabel* m_idNO;
	QLineEdit* m_idNOEdit;

	QLabel* m_ticktNO;
	QLineEdit* m_ticktNOEdit;

};

//--------------------------------------------------
class WristWatchInfoWidget :public QWidget
{
	Q_OBJECT
public:
	explicit WristWatchInfoWidget(QWidget* parent = 0);
	~WristWatchInfoWidget();
protected:
	void paintEvent(QPaintEvent *);
public slots:
	void setSmartCardData(QString NO,QString Stat);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	CommentBindTopWidget* m_head;
	QLabel* m_watchID;
	QLineEdit* m_watchIDEdit;

	QLabel* m_watchStat;
	QLineEdit* m_watchStatEdit;
};

//-----------------------------------------------------
class BindWidget :public QWidget
{
	Q_OBJECT
public:
	explicit BindWidget(QWidget* parent = 0);
	~BindWidget();
	void StopTime();
	void StartTime();

	
protected:
	void paintEvent(QPaintEvent *);
signals:
	void turnMainContentPage(int curIndex);

	void SigAddWatch(QString data);
public slots:
	void turnPage(int curIndex);
	void setMainContentPage();
	void getExam(QString name);

	void popTip(QString tip);
	void popWatch(QString tip);
	void popBind(QString tip);
	void cacheIDCardData(QString stuName, QString stuNO, QString idNO, QString ticketNO);

	void cacheSmartCardData(QString NO, QString Stat);

	void changeAddWatch(QString data);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QLabel* m_showExam;

	BindTip* m_tip;
	BindNoTip* m_notip;
	StudentInfoWidget* m_studentInfo;
	WristWatchInfoWidget* m_watchInfo;
	ActivityLabel* m_bind;

	ActivityLabel* m_watchManager;

	QStackedWidget* m_stackedWidget;

	ReadAgency* m_readAgency;
};

#endif