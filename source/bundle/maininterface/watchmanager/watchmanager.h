#ifndef WATCHMANAGER
#define WATCHMANAGER

#include <QWidget>
#include <QLabel>
#include <QLineEdit>
#include <QComboBox>
#include <QStackedWidget>
#include "watchview.h"
#include "pagingwidget.h"
#include "../../public/activitylabel.h"
#include "../../public/dropShadow.h"
#include "../../public/tip.h"

class WatchManagerTopWidget :public QWidget
{
	Q_OBJECT
public:
	explicit WatchManagerTopWidget(QWidget* parent = 0);
	~WatchManagerTopWidget();
	void hideBack();
signals:
	void turnMainContentPage(int curIndex);
public slots:
	void setMainContentPage();
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QWidget* m_topborder;
	QLabel* m_name;
	ActivityLabel* m_pic;
	QWidget* m_bottom;

};

//-------------------------------------------------------------------------------------------
class WatchContentSearch :public QWidget
{
	Q_OBJECT
public:
	explicit WatchContentSearch(QWidget* parent = 0);
	~WatchContentSearch();
	void init();
signals:
	void search(QString id,QString stat);
public slots :
	void dealSearch();
	void setIndex(int data);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QLineEdit* m_deviceInputID;
	QComboBox* m_statSelect;
	ActivityLabel* m_search;

	int index;
};

//--------------------------------------------------
class WatchManagerContentWidget :public QWidget
{
	Q_OBJECT
public:
	explicit WatchManagerContentWidget(QWidget* parent = 0);
	~WatchManagerContentWidget();
	void SigAddWatch(QString data);
	void LoadData();
	void initSearch();
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	WatchContentSearch* m_search;
	WatchView* m_view;
	PagingWidget* m_page;
};

//--------------------------------------------------
class WatchManagerWidget :public QWidget
{
	Q_OBJECT
public:
	explicit WatchManagerWidget(QWidget* parent = 0);
	~WatchManagerWidget();
	void SigAddWatch(QString data);
	void LoadData();
	void initSearch();
	void setTop();
protected:
	void paintEvent(QPaintEvent *);
signals:
	void turnMainContentPage(int curIndex);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	WatchManagerTopWidget* m_top;
	WatchManagerContentWidget* m_content;
};

//--------------------------------------------------
class WatchManager :public QWidget
{
	Q_OBJECT
public:
	explicit WatchManager(QWidget* parent = 0);
	~WatchManager();
	void LoadData();
	void initSearch();
protected:
	void paintEvent(QPaintEvent *);
signals:
	void turnMainContentPage(int curIndex);
public slots:
	void SigAddWatch(QString data);
	void slotNoExam(QString name);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	/*BindTip* m_tip;
	BindNoTip* m_notip;
	QStackedWidget* m_stackedWidget;*/

	QLabel* m_tip;
	WatchManagerWidget* m_content;
};

#endif