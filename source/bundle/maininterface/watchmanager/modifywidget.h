#ifndef MODIFYWIDGET
#define MODIFYWIDGET

#include <QWidget>
#include <QPaintEvent>
#include <QLabel>
#include <QLineEdit>
#include <QComboBox>
#include "../../public/activitylabel.h"
#include "../../public/dropShadow.h"
#include "../../public/calendar.h"

class ModifyWidget;
class ModifyHeadWidget :public QWidget
{
	Q_OBJECT
public:
	explicit ModifyHeadWidget(QWidget* parent = 0);
	~ModifyHeadWidget();
	void setTagName(QString name);
protected:
	void mousePressEvent(QMouseEvent *event);
	void mouseReleaseEvent(QMouseEvent *event);
	void mouseMoveEvent(QMouseEvent *event);
public slots:
	void sigclose();
signals:
	void CloseWidget();
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QLabel* m_tagname;
	ActivityLabel* m_close;
	QWidget* m_bottom;


	QPoint move_point;
	bool mouse_press;
};

//--------------------------------------------------
class ModifyContentWidget :public QWidget
{
	Q_OBJECT
public:
	explicit ModifyContentWidget(QWidget* parent = 0);
	~ModifyContentWidget();
	void setAddWatch(QString data);
	void setStatIndex(int index);
	void setOperateStat(int stat);
	void reqBycode(QString code);
signals:
	void closePop();
	void CloseWidget();
public slots:
	void calendarSlot(QWidget* widget);
	void setPurchaseTimeEdit(QString date);
	void setIndex(int data);
	void SubmitData();
	void recevieRequest(int stat, QString data, int type);
	void recevieByCode(int stat, QString data);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	void setAllData(QString code, QString factory, QString sp,QString status, QString purchase_dt);

	QLabel* m_deviceID;
	QLineEdit* m_deviceIDInput;

	QLabel* m_vender;
	QLineEdit* m_venderInput;

	QLabel* m_type;
	QLineEdit* m_typeInput;

	QLabel* m_purchaseDate;
	ClickEdit* m_purchaseDateInput;

	QLabel* m_stat;
	QComboBox* m_statInput;

	ActivityLabel* m_confirm;
	ActivityLabel* m_cancel;

	QWidget* m_seperete;

	int index;

	int operateStat;//0ÐÞ¸Ä 1Ìí¼Ó
};

//--------------------------------------------------
class ModifyWidget :public DropShadowWidget
{
	Q_OBJECT
public:
	explicit ModifyWidget(QWidget* parent=0);
	~ModifyWidget();
	void setTagName(QString name);
	void setAddWatch(QString data);
	void setStatIndex(int index);
	void setOperateStat(int stat);
	void reqBycode(QString code);
protected:
	void paintEvent(QPaintEvent *);
signals:
	void CloseWidget();
private:
	void createUI();
	void setLayoutUI();
	void setConnection();


	ModifyHeadWidget* m_head;
	ModifyContentWidget* m_content;
};
#endif