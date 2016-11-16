#include "maininterface/mainwidget.h"
#include <QApplication>
#include <QTextCodec>
#if _MSC_VER >= 1600
#pragma execution_character_set("utf-8")
#endif

#include "maininterface/comment.h"
#include "maininterface/httpapi/httprequest.h"

HttpRequest request;
int main(int argc, char *argv[])
{
	QApplication a(argc, argv);

	//QTextCodec::setCodecForLocale(QTextCodec::codecForName("gbk"));

	QFile qss(":/qss/style");
	qss.open(QFile::ReadOnly);
	a.setStyleSheet(qss.readAll());
	qss.close();

	Q_INIT_RESOURCE(bundle);

	
	//request.LoginPost();
	

	MainWidget mainWidget;
	mainWidget.show();
	return a.exec();
}
