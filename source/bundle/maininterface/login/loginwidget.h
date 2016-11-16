#ifndef LOGINWIDGET
#define LOGINWIDGET

#include <QWidget>
#include <QLabel>
#include <QLineEdit>
#include <QStackedWidget>
#include "../../public/activitylabel.h"
#include "../../public/label.h"
#include "../../public/tip.h"


class LoginContentWidget :public QWidget
{
	Q_OBJECT
public:
	explicit LoginContentWidget(QWidget* parent = 0);
	~LoginContentWidget();
protected:
	void paintEvent(QPaintEvent *);
signals:
	void turnMainContentPage(int curIndex);
	void setExamViewData(QStringList ids,QStringList names);
	void setip(QString name);

	void sigNoExam(QString name);
public slots:
	void changeRemeber();
	void login();
	void requstExamList();
	void recevieRequest(int stat, QString data, int type);
	void DealLogin(int stat, QString data);
	void DealExamList(int stat, QString data);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QWidget* m_head;

	QLabel* m_serverip; 
	QLineEdit* m_serveripInput;

	QLabel* m_user;
	QLineEdit* m_userInput;

	QLabel* m_password;
	QLineEdit* m_passwordInput;

	ActivityLabel* m_remeberPic;
	QLabel* m_remeberTag;

	//Label* m_forgetPass;
	ActivityLabel* m_login;

	bool isRemeber;
};

//----------------------------------
class LoginWidget :public QWidget
{
	Q_OBJECT
public:
	explicit LoginWidget(QWidget* parent = 0);
	~LoginWidget();
signals:
	void turnMainContentPage(int curIndex);
	void setExamViewData(QStringList ids, QStringList names);
	void sigNoExam(QString name);
protected:
	void paintEvent(QPaintEvent *);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	/*BindTip* m_tip;
	BindNoTip* m_notip;
	QStackedWidget* m_stackedWidget;*/

	LoginContentWidget* m_content;
};
#endif