#ifndef TIP
#define TIP

#include <QWidget>
#include <QLabel>
#include <QTimer>
#include "../public/activitylabel.h"

class BindNoTip :public QWidget
{
	Q_OBJECT
public:
	explicit BindNoTip(QWidget* parent = 0);
	~BindNoTip();
};

//--------------------------------------------------
class BindTip :public QWidget
{
	Q_OBJECT
public:
	explicit BindTip(QWidget* parent = 0);
	~BindTip();
protected:
	void paintEvent(QPaintEvent *);
	public slots:
	void setPage();

	void setTip(QString tip);
	void setWatchTip(QString tip);
	void setBindTip(QString tip);

	void clearTip();
	void StartClearClock();
	void StopClearClock();
signals:
	void turnPage(int curIndex);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	ActivityLabel* m_tipic;
	QLabel* m_tip;
	QLabel* m_tipWatch;
	QLabel* m_tipBind;
	ActivityLabel* m_close;

	QTimer* m_clearClock;

};


#endif