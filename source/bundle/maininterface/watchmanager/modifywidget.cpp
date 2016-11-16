#include "modifywidget.h"
#include <QPainter>
#include <QStyleOption>
#include <QListView>
#include <QHBoxLayout>
#include <QVBoxLayout>
#include <QFormLayout>
#include <QDateTime>
#include "../httpapi/httprequest.h"

extern HttpRequest request;

ModifyHeadWidget::ModifyHeadWidget(QWidget* parent) :QWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
	mouse_press = false;
}

ModifyHeadWidget::~ModifyHeadWidget()
{

}

void ModifyHeadWidget::createUI()
{
	QFont ft;
	ft.setPointSize(12);
	ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft.setBold(true);
	m_tagname = new QLabel(this);
	m_tagname->setFixedSize(100, 30);
	m_tagname->setText(QStringLiteral("±à¼­"));
	m_tagname->setFont(ft);
	m_tagname->setStyleSheet("color:rgb(22,190,176);");

	m_close = new ActivityLabel(this);
	m_close->setPicName(QString(":/img/closepop"));

	m_bottom = new QWidget(this);
	m_bottom->setFixedHeight(1);
	m_bottom->setStyleSheet("border:0px;background-color:rgb(227,227,227);");

	this->setFixedHeight(50);
}

void ModifyHeadWidget::setLayoutUI()
{
	QHBoxLayout* hLayout = new QHBoxLayout();
	hLayout->setMargin(0);
	hLayout->addWidget(m_tagname);
	hLayout->addStretch();
	hLayout->addWidget(m_close);
	hLayout->setSpacing(0);
	hLayout->setContentsMargins(10,0,10,0);

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addStretch();
	mainLayout->addLayout(hLayout);
	mainLayout->addStretch();
	mainLayout->addWidget(m_bottom);
	mainLayout->setSpacing(0);
	mainLayout->setContentsMargins(0,0,0,0);

	this->setLayout(mainLayout);
}

void ModifyHeadWidget::setConnection()
{
	connect(m_close,SIGNAL(Lclicked()),this,SLOT(sigclose()));
}

void ModifyHeadWidget::setTagName(QString name)
{
	m_tagname->setText(name);
}

void ModifyHeadWidget::sigclose()
{
	emit CloseWidget();
}

void ModifyHeadWidget::mousePressEvent(QMouseEvent *event)
{
	if (event->button() == Qt::LeftButton)
	{
		mouse_press = true;
	}
	ModifyWidget *pMainWidget = (qobject_cast<ModifyWidget *>(parent()));

	move_point = event->globalPos() - pMainWidget->pos();
}

void ModifyHeadWidget::mouseReleaseEvent(QMouseEvent *)
{
	mouse_press = false;
}

void ModifyHeadWidget::mouseMoveEvent(QMouseEvent *event)
{
	if (mouse_press)
	{
		QPoint move_pos = event->globalPos();
		ModifyWidget *pMainWidget = (qobject_cast<ModifyWidget *>(parent()));
		pMainWidget->move(move_pos - move_point);
	}
}



//--------------------------------------------------
ModifyContentWidget::ModifyContentWidget(QWidget* parent) :QWidget(parent)
{
	index = -1;
	operateStat = 0;
	createUI();
	setLayoutUI();
	setConnection();
}

ModifyContentWidget::~ModifyContentWidget()
{

}

void ModifyContentWidget::createUI()
{
	QFont ft1;
	ft1.setPointSize(12);
	ft1.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
	ft1.setBold(true);

	QFont ft2;
	ft2.setPointSize(12);
	ft2.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));


	m_deviceID = new QLabel(this);
	m_deviceID->setFixedSize(80,30);
	m_deviceID->setText(QStringLiteral("Éè±¸ID"));
	m_deviceID->setAlignment(Qt::AlignVCenter|Qt::AlignRight);
	m_deviceID->setFont(ft1);
	m_deviceID->setStyleSheet("color:rgb(103,106,108);");

	m_deviceIDInput = new QLineEdit(this);
	m_deviceIDInput->setFixedSize(370, 30);
	m_deviceIDInput->setFont(ft2);
	m_deviceIDInput->setAlignment(Qt::AlignVCenter);
	m_deviceIDInput->setStyleSheet("border:1px solid rgb(229,230,231);color:rgb(184,184,184);");
	m_deviceIDInput->setContextMenuPolicy(Qt::NoContextMenu);

	m_vender = new QLabel(this);
	m_vender->setFixedSize(80, 30);
	m_vender->setText(QStringLiteral("³§¼Ò"));
	m_vender->setAlignment(Qt::AlignVCenter | Qt::AlignRight);
	m_vender->setFont(ft1);
	m_vender->setStyleSheet("color:rgb(103,106,108);");

	m_venderInput = new QLineEdit(this);
	m_venderInput->setFixedSize(370, 30);
	m_venderInput->setFont(ft2);
	m_venderInput->setAlignment(Qt::AlignVCenter);
	m_venderInput->setStyleSheet("border:1px solid rgb(229,230,231);color:rgb(184,184,184);");
	m_venderInput->setContextMenuPolicy(Qt::NoContextMenu);

	m_type = new QLabel(this);
	m_type->setFixedSize(80, 30);
	m_type->setText(QStringLiteral("ÐÍºÅ"));
	m_type->setAlignment(Qt::AlignVCenter | Qt::AlignRight);
	m_type->setFont(ft1);
	m_type->setStyleSheet("color:rgb(103,106,108);");

	m_typeInput = new QLineEdit(this);
	m_typeInput->setFixedSize(370, 30);
	m_typeInput->setFont(ft2);
	m_typeInput->setAlignment(Qt::AlignVCenter);
	m_typeInput->setStyleSheet("border:1px solid rgb(229,230,231);color:rgb(184,184,184);");
	m_typeInput->setContextMenuPolicy(Qt::NoContextMenu);


	m_purchaseDate = new QLabel(this);
	m_purchaseDate->setFixedSize(80, 30);
	m_purchaseDate->setText(QStringLiteral("²É¹ºÈÕÆÚ"));
	m_purchaseDate->setAlignment(Qt::AlignVCenter | Qt::AlignRight);
	m_purchaseDate->setFont(ft1);
	m_purchaseDate->setStyleSheet("color:rgb(103,106,108);");

	m_purchaseDateInput = new ClickEdit(this);
	m_purchaseDateInput->setFixedSize(370, 30);
	m_purchaseDateInput->setReadOnly(true);
	m_purchaseDateInput->setFont(ft2);
	m_purchaseDateInput->setAlignment(Qt::AlignVCenter);
	m_purchaseDateInput->setStyleSheet("border:1px solid rgb(229,230,231);color:rgb(184,184,184);");
	m_purchaseDateInput->setContextMenuPolicy(Qt::NoContextMenu);

	m_stat = new QLabel(this);
	m_stat->setFixedSize(80, 30);
	m_stat->setText(QStringLiteral("×´Ì¬"));
	m_stat->setAlignment(Qt::AlignVCenter | Qt::AlignRight);
	m_stat->setFont(ft1);
	m_stat->setStyleSheet("color:rgb(103,106,108);");

	m_statInput = new QComboBox(this);
	QStringList items;
	items << QStringLiteral("ÇëÑ¡ÔñÉè±¸×´Ì¬") << QStringLiteral("Î´Ê¹ÓÃ") << QStringLiteral("Ê¹ÓÃÖÐ") << QStringLiteral("Î¬ÐÞ") << QStringLiteral("±¨·Ï");
	m_statInput->setFixedSize(370, 30);
	m_statInput->addItems(items);
	m_statInput->setContextMenuPolicy(Qt::NoContextMenu);

	m_statInput->setView(new QListView());
	m_statInput->setEditable(true);
	//m_statSelect->setLineEdit(new QTComboBoxButton(m_pAnswer));
	m_statInput->lineEdit()->setReadOnly(true);
	m_statInput->setMaxVisibleItems(m_statInput->count());
	m_statInput->lineEdit()->setText(QStringLiteral("ÇëÑ¡ÔñÉè±¸×´Ì¬"));
	m_statInput->lineEdit()->setStyleSheet("color:rgb(184,184,184);");
	m_statInput->lineEdit()->setFont(ft2);
	m_statInput->lineEdit()->setTextMargins(10, 0, 0, 0);

	m_statInput->view()->setStyleSheet("QListView {font-family: \"Î¢ÈíÑÅºÚ\"; font-size: 12px; outline: 0px;}"
		"QListView::item {padding: 3px 0x 3px 5px; border-width: 2px;}"
		"QListView::item:selected {background-color: rgb(74, 144, 226);}");

	m_confirm = new ActivityLabel(this);
	m_confirm->setPicName(QString(":/img/btn_save"));

	m_cancel = new ActivityLabel(this);
	m_cancel->setPicName(QString(":/img/btn_back"));

	m_seperete = new QWidget(this);
	m_seperete->setFixedHeight(60);
	m_seperete->setStyleSheet("border:0px;backgorund:transparent");
}

void ModifyContentWidget::setLayoutUI()
{
	QHBoxLayout* hDeviceID = new QHBoxLayout();
	hDeviceID->setMargin(0);
	hDeviceID->addWidget(m_deviceID);
	hDeviceID->addSpacing(5);
	hDeviceID->addWidget(m_deviceIDInput);
	hDeviceID->setContentsMargins(0,0,0,0);

	QHBoxLayout* hVender = new QHBoxLayout();
	hVender->setMargin(0);
	hVender->addWidget(m_vender);
	hVender->addSpacing(5);
	hVender->addWidget(m_venderInput);
	hVender->setContentsMargins(0, 0, 0, 0);

	QHBoxLayout* hType = new QHBoxLayout();
	hType->setMargin(0);
	hType->addWidget(m_type);
	hType->addSpacing(5);
	hType->addWidget(m_typeInput);
	hType->setContentsMargins(0, 0, 0, 0);

	QHBoxLayout* hPurchaseDate = new QHBoxLayout();
	hPurchaseDate->setMargin(0);
	hPurchaseDate->addWidget(m_purchaseDate);
	hPurchaseDate->addSpacing(5);
	hPurchaseDate->addWidget(m_purchaseDateInput);
	hPurchaseDate->setContentsMargins(0, 0, 0, 0);

	QHBoxLayout* hStat = new QHBoxLayout();
	hStat->setMargin(0);
	hStat->addWidget(m_stat);
	hStat->addSpacing(5);
	hStat->addWidget(m_statInput);
	hStat->setContentsMargins(0,0,0,0);

	QFormLayout* form = new QFormLayout();
	form->addRow(hDeviceID);
	form->addRow(hVender);
	form->addRow(hType);
	form->addRow(hPurchaseDate);
	form->addRow(hStat);
	//form->addRow();
	form->setVerticalSpacing(18);

	QVBoxLayout* vForm = new QVBoxLayout();
	vForm->addLayout(form);
	//vForm->addStretch();
	vForm->setContentsMargins(10, 0, 0, 0);

	QHBoxLayout* hBtn = new QHBoxLayout();
	hBtn->setMargin(0);
	hBtn->addStretch();
	hBtn->addWidget(m_cancel);
	hBtn->addSpacing(10);
	hBtn->addWidget(m_confirm);
	hBtn->setContentsMargins(10, 0, 35, 0);
	//hBtn->addStretch();

	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addLayout(vForm);
	mainLayout->addStretch();
	mainLayout->addWidget(m_seperete);
	mainLayout->addStretch();
	mainLayout->addLayout(hBtn);
	mainLayout->setContentsMargins(0, 20, 0, 20);

	this->setLayout(mainLayout);
}

void ModifyContentWidget::setConnection()
{
	connect(&request, SIGNAL(requestData(int, QString, int)), this, SLOT(recevieRequest(int, QString, int)));
	connect(m_purchaseDateInput, SIGNAL(clicked(QWidget*)), this, SLOT(calendarSlot(QWidget*)));
	connect(m_cancel, SIGNAL(Lclicked()), this, SIGNAL(CloseWidget()));
	connect(m_statInput, SIGNAL(currentIndexChanged(int)), this, SLOT(setIndex(int)));
	connect(m_confirm, SIGNAL(Lclicked()), this, SLOT(SubmitData()));
}

void ModifyContentWidget::calendarSlot(QWidget* widget)
{
	CalendarMenu* menu = new CalendarMenu();
	connect(menu, SIGNAL(closeWidget()), menu, SLOT(close()));
	if (widget == m_purchaseDateInput)
	{
		connect(menu, SIGNAL(retDate(QString)), this, SLOT(setPurchaseTimeEdit(QString)));
	}
	connect(this, SIGNAL(closePop()), menu, SLOT(close()));
	QPoint pos;
	pos.setX(0);
	pos.setY(widget->sizeHint().height()+8);
	menu->exec(widget->mapToGlobal(pos));
}

void ModifyContentWidget::setPurchaseTimeEdit(QString date)
{
	m_purchaseDateInput->setText(date);
	emit closePop();
}

void ModifyContentWidget::setAddWatch(QString data)
{
	m_deviceIDInput->setText(data);
	m_deviceIDInput->setReadOnly(true);
}

void ModifyContentWidget::setStatIndex(int index)
{
	this->index = index;
	m_statInput->setCurrentIndex(this->index + 1);
	m_statInput->setDisabled(true);
}

void ModifyContentWidget::setIndex(int data)
{
	if (data <= 0)
	{
		index = -1;
	}
	this->index = data - 1;
}

void ModifyContentWidget::setOperateStat(int stat)
{
	this->operateStat = stat;
}

void ModifyContentWidget::SubmitData()
{
	QString code = m_deviceIDInput->text();
	QString status = QString::number(this->index,10);
	QString factory = m_venderInput->text();
	QString sp = m_typeInput->text();
	QString purchaseDate = m_purchaseDateInput->text();
	if (0 == operateStat)
	{
		request.ModWatchsByManager(code, "", status, "", factory, sp, purchaseDate, WatchManagerMod);
	}
	if (1== operateStat)
	{
		request.AddWatchsByManager(code, "", status, "", factory, sp, purchaseDate, WatchManagerAdd);
	}
}

void ModifyContentWidget::reqBycode(QString code)
{
	request.GetWatchByManagerByCode(code, WatchManagerDet);
}

void ModifyContentWidget::recevieRequest(int stat, QString data, int type)
{
	if (type == WatchManagerAdd || type == WatchManagerMod)
	{
		qDebug() << stat << endl;
		if (stat != 200)
		{
			emit CloseWidget();
			return;
		}
		qDebug() << data << endl;
		QJsonParseError error;
		QJsonDocument jsonDocument = QJsonDocument::fromJson(data.toUtf8(), &error);

		if (error.error == QJsonParseError::NoError) {
			if (!(jsonDocument.isNull() || jsonDocument.isEmpty()))
			{
				emit CloseWidget();
			}
		}
		else {
			//qFatal(error.errorString().toUtf8().constData());
			//exit(1);
			emit CloseWidget();
		}
	}

	if (type == WatchManagerDet)
	{
		qDebug() << stat << endl;
		recevieByCode(stat,data);
	}
}

void ModifyContentWidget::recevieByCode(int stat, QString data)
{
	qDebug() << "stat=" << stat << endl;
	if (stat != 200)
	{
		return;
	}
	qDebug() << data << endl;
	QJsonParseError error;
	QJsonDocument jsonDocument = QJsonDocument::fromJson(data.toUtf8(), &error);

	if (error.error == QJsonParseError::NoError) {
		if (!(jsonDocument.isNull() || jsonDocument.isEmpty()))
		{
			if (jsonDocument.isObject())
			{
				QVariantMap result = jsonDocument.toVariant().toMap();
				int stat = result["code"].toInt();
				if (stat != 1)
					return;
				QVariantList litt = result["data"].toList();
				foreach(QVariant plugin, litt)
				{
					QVariantMap listmap = plugin.toMap();
					QString code = listmap["code"].toString();
					QString factory = listmap["factory"].toString();
					QString sp = listmap["sp"].toString();
					QString status = listmap["status"].toString();
					QString purchase_dt = listmap["purchase_dt"].toString();

					QDateTime time = QDateTime::fromString(purchase_dt, "yyyy-MM-dd hh:mm:ss");
					QString purchase= time.toString("yyyy-MM-dd");
					setAllData(code, factory, sp, status, purchase);
					//QStringList record;
				}
			}
			else
			{

			}
		}
	}
	else {
		//qFatal(error.errorString().toUtf8().constData());
		//exit(1);
	}
}

void ModifyContentWidget::setAllData(QString code, QString factory, QString sp, QString status, QString purchase_dt)
{
	m_deviceIDInput->setText(code);
	m_deviceIDInput->setReadOnly(true);

	m_venderInput->setText(factory);
	m_typeInput->setText(sp);

	m_purchaseDateInput->setText(purchase_dt);

	int sindex = status.toInt();
	this->index = sindex;
	m_statInput->setCurrentIndex(this->index + 1);
}

//--------------------------------------------------
ModifyWidget::ModifyWidget(QWidget* parent) :DropShadowWidget(parent)
{
	createUI();
	setLayoutUI();
	setConnection();
}

ModifyWidget::~ModifyWidget()
{

}

void ModifyWidget::createUI()
{
	m_head = new  ModifyHeadWidget(this);
	m_content = new ModifyContentWidget(this);

	this->setObjectName("modify");
	this->setFixedSize(500,404);
}

void ModifyWidget::setLayoutUI()
{
	QVBoxLayout* mainLayout = new QVBoxLayout(this);
	mainLayout->setMargin(0);
	mainLayout->addWidget(m_head);
	mainLayout->addWidget(m_content);
	mainLayout->addStretch();
	mainLayout->setContentsMargins(0,0,0,0);
	this->setLayout(mainLayout);
}

void ModifyWidget::setConnection()
{
	connect(m_head, SIGNAL(CloseWidget()), this, SIGNAL(CloseWidget()));
	connect(m_content, SIGNAL(CloseWidget()), this, SIGNAL(CloseWidget()));
}

void ModifyWidget::setTagName(QString name)
{
	m_head->setTagName(name);
}

void ModifyWidget::setAddWatch(QString data)
{
	m_content->setAddWatch(data);
}

void ModifyWidget::setStatIndex(int index)
{
	m_content->setStatIndex(index);
}

void ModifyWidget::setOperateStat(int stat)
{
	m_content->setOperateStat(stat);
}

void ModifyWidget::reqBycode(QString code)
{
	m_content->reqBycode(code);
}

void ModifyWidget::paintEvent(QPaintEvent *)
{
	QStyleOption opt;
	opt.init(this);
	QPainter p(this);
	style()->drawPrimitive(QStyle::PE_Widget, &opt, &p, this);
}