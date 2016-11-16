#ifndef MAINHEAD
#define MAINHEAD

#include <QWidget>
#include "../public/activitylabel.h"

class MainHead :public QWidget
{
	Q_OBJECT
public:
	explicit MainHead(QWidget* parent = 0);
	~MainHead();
protected:
	void mousePressEvent(QMouseEvent *event);
	void mouseReleaseEvent(QMouseEvent *event);
	void mouseMoveEvent(QMouseEvent *event);
	void paintEvent(QPaintEvent *);
signals:
	void showMin();
	void showMax();
	void closeWidget();
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QPoint move_point;
	bool mouse_press;

	ActivityLabel* m_logo;
	ActivityLabel* m_sysSet;
	ActivityLabel* m_minBtn;
	ActivityLabel* m_maxBtn;
	ActivityLabel* m_closeBtn;
};


#endif // MAINHEAD
