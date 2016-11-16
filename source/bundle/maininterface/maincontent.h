#ifndef MAINCONTENT
#define MAINCONTENT

#include <QWidget>
#include <QStackedWidget>
#include "bind/bindwidget.h"
#include "watchmanager/watchmanager.h"
#include "login/loginwidget.h"
#include "selectexam/selectexamwidget.h"

enum MainContentIndex
{
	Stacked_Bind = 0,
	Stacked_WatchManager,
	Stacked_Login,
	Stacked_Exam,
};

class MainContent :public QWidget
{
	Q_OBJECT
public:
	explicit MainContent(QWidget* parent = 0);
	~MainContent();
protected:
	void paintEvent(QPaintEvent *);
public slots:
	void turnPage(int curIndex);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	BindWidget* m_bind;
	WatchManager* m_watchManager;
	LoginWidget* m_login;
	SelectExamWidget* m_selectexam;
	QStackedWidget* m_stackedWidget;
};

#endif // MAINCONTENT
