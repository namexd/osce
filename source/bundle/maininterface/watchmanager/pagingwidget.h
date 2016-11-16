#ifndef PAGINGWIDGET
#define PAGINGWIDGET

#include <QWidget>
#include <QLabel>
#include <QLineEdit>
#include <QComboBox>
#include <QSignalMapper>
#include "watchview.h"
#include "../../public/activitylabel.h"
#include "../../public/label.h"

class PagingWidget :public QWidget
{
	Q_OBJECT
public:
	explicit PagingWidget(QWidget* parent = 0);
	~PagingWidget();
	void setWatchView(WatchView* view);
public slots:
	void pageBtnClick(QString curPage);
	void pageBtnHover(QString curPage);
	void pageBtnLeave(QString curPage);
	void changeNextPage();
	void changePrePage();


	void restPage();
private:
	void setStartButton();
	void changePage();
	void clickButtonChangePreBak(int cpage);
	void changeHoverButton(int cur,int index);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QLabel* m_totalPage;
	ActivityLabel*  m_prepage;
	ActivityLabel*  m_bakpage;
	QList<Label *>  m_buttons;
	QSignalMapper *signalMapperClick;
	QSignalMapper *signalMapperHover;
	QSignalMapper *signalMapperLeave;

	WatchView* m_view;
	int indexClick;
	int totalPage;

	int perPage;
	int curentPage;

	bool isSetPicPre;//先前翻不可点
	bool isSetPicBak;//向后翻不可点
};
#endif