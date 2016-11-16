#ifndef SELECTEXAMWIDGET
#define SELECTEXAMWIDGET

#include <QWidget>
#include <QLabel>
#include <QLineEdit>
#include "../../public/activitylabel.h"
#include "../../public/label.h"
#include "examlist.h"


class SelectExamContentWidget :public QWidget
{
	Q_OBJECT
public:
	explicit SelectExamContentWidget(QWidget* parent = 0);
	~SelectExamContentWidget();
	void setExamViewData(QStringList ids, QStringList names);
signals:
	void turnMainContentPage(int curIndex);
protected:
	void paintEvent(QPaintEvent *);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	QWidget* m_head;
	QLabel* m_tag;
	ExamView* m_view;
};

//----------------------------------
class SelectExamWidget :public QWidget
{
	Q_OBJECT
public:
	explicit SelectExamWidget(QWidget* parent = 0);
	~SelectExamWidget();
signals:
	void turnMainContentPage(int curIndex);
public slots:
	void setExamViewData(QStringList ids, QStringList names);
protected:
	void paintEvent(QPaintEvent *);
private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	SelectExamContentWidget* m_content;
};

#endif