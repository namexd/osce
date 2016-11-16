#ifndef MAINWIDGET
#define MAINWIDGET

#include <QWidget>
#include "../public/dropShadow.h"
#include "mainhead.h"
#include "maincontent.h"

class MainWidget :public DropShadowWidget
{
	Q_OBJECT

public:
	explicit MainWidget(QWidget *parent = 0);
	~MainWidget();
public slots:
	void widgetSizeSwitch();
protected:


signals :

private:
	void createUI();
	void setLayoutUI();
	void setConnection();

	MainHead* m_head;
	MainContent* m_content;
};
#endif // MAINWIDGET