#include "watchview.h"
#include <QPainter>
#include <QMouseEvent>
#include <QStyleOption>
#include <QApplication>
#include <QStyledItemDelegate>
#include <QDateTime>
#include <QDesktopWidget>
#include <QApplication>
#include <QDebug>
#include "../mainwidget.h"
#include "modifywidget.h"
#include "../httpapi/httprequest.h"
#include <QJsonArray>
#include <QJsonObject>
#include <QJsonDocument>
#include <QJsonParseError>

extern HttpRequest request;

WatchModel::WatchModel(QObject* parent):QAbstractTableModel(parent)
{
	connect(&request, SIGNAL(requestData(int, QString, int)), this, SLOT(recevieRequest(int, QString, int)));
	QStringList header;
	header << QStringLiteral("设备ID") << QStringLiteral("使用人") << QStringLiteral("状态")<< QStringLiteral("操作");
	setHorizontalHeaderList(header);
	//LoadData();
}

WatchModel::~WatchModel()
{

}

void WatchModel::setHorizontalHeaderList(QStringList horizontalHeaderList)
{
	m_horizontalHeaderList = horizontalHeaderList;
}

int WatchModel::rowCount(const QModelIndex &parent) const
{
	Q_UNUSED(parent);
	return m_tableValue.size();
}

int WatchModel::columnCount(const QModelIndex &parent) const
{
	Q_UNUSED(parent);
	return m_horizontalHeaderList.size();
}


QVariant WatchModel::data(const QModelIndex &index, int role) const
{
	if (!index.isValid())
		return QVariant();

	if (role == Qt::TextAlignmentRole)
	{
		return int(Qt::AlignLeft | Qt::AlignVCenter);
	}
	if (role == Qt::DisplayRole)
	{
		int ncol = index.column();
		int nrow = index.row();
		QStringList values = m_tableValue.at(nrow);
		if (values.size() > ncol)
		{
			if (ncol == 2)
			{
				return getWatchStat(values.at(ncol));
			}
			else
			{
				return values.at(ncol);
			}
		}
		else
			return QVariant();
	}
	if (role == Qt::FontRole)
	{
		QFont ft;
		ft.setFamily(trUtf8("微软雅黑"));
		ft.setPointSize(12);
		return QFont(ft);
	}
	if (role == Qt::BackgroundColorRole)
	{
		if (index.row() % 2 == 0)
		{
			return  QColor(242, 242, 242);
		}
		else if (index.row() % 2 == 1)
		{
			return  QColor(Qt::white);
		}
		else
		{
			return QVariant();
		}
	}

	/*if (role == Qt::TextColorRole && index.column() == 2)
	{
		return QColor(Qt::blue);
	}*/

	return QVariant();
}


QVariant WatchModel::headerData(int section, Qt::Orientation orientation, int role) const
{
	if (role == Qt::DisplayRole && orientation == Qt::Horizontal)
	{

		if (m_horizontalHeaderList.size() >= section)
			return m_horizontalHeaderList[section];
		else
			return QVariant();
	}
	if (role == Qt::TextAlignmentRole)
	{
		return int(Qt::AlignLeft | Qt::AlignVCenter);
	}
	if (role == Qt::FontRole)
	{
		QFont ft;
		ft.setFamily(trUtf8("微软雅黑"));
		ft.setPointSize(12);
		ft.setBold(true);
		return QFont(ft);
	}

	return QVariant();
}

Qt::ItemFlags WatchModel::flags(const QModelIndex &index) const
{
	if (!index.isValid())
		return 0;
	return QAbstractItemModel::flags(index);
}

void WatchModel::setData(QStringList  data)
{
	m_tableValue.append(data);
	emit this->layoutChanged();
}

void WatchModel::LoadData()
{
	m_tableValue.clear();
	emit this->layoutChanged();
	emit loadPage();
	request.GetWatchByManager(WatchManagerGet);
}

void WatchModel::deleteRow(QModelIndex index)
{
	request.DelWatchsByManager(getCode(index), WatchManagerDel);
}

void WatchModel::recevieRequest(int stat, QString data, int type)
{
	if (type == WatchManagerGet || type == WatchManagerSea)
	{
		recevieALL(stat, data);
	}

	if (type == WatchManagerDel)
	{
		recevieDel(stat,data);
	}
}

void WatchModel::recevieDel(int stat, QString data)
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
				if (stat == 1)
				{
					LoadData();
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
void WatchModel::recevieALL(int stat, QString data)
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
					QString id = listmap["code"].toString();
					QString studentName = listmap["studentName"].toString();
					QString status = listmap["status"].toString();

					QStringList record;
					record << id << studentName << status;
					m_tableValue.append(record);
					//qDebug() << id << "  " << studentName << "  " << status << endl;
				}
				emit this->layoutChanged();
				emit loadPage(); 
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

void WatchModel::LoadSearch(QString id, QString stat)
{
	m_tableValue.clear();
	emit this->layoutChanged();
	emit loadPage();
	request.GetWatchByManagerSearch(id, stat, WatchManagerSea);
}

QString WatchModel::getWatchStat(QString st) const
{
	QString retStat;
	retStat.clear();
	int stat = st.toInt();
	switch (stat)
	{
	case 0:
	{
								retStat = QStringLiteral("未使用");
								break;
	}
	case 1:
	{
										 retStat = QStringLiteral("使用中");
										 break;
	}
	case 2:
	{
								 retStat = QStringLiteral("维修");
								 break;
	}
	case 3:
	{
								  retStat = QStringLiteral("报废");
								  break;
	}
	default:
	{
			   break;
	}
	}
	return retStat;
}


QString WatchModel::getCode(QModelIndex index)
{
	QString retStat;
	int ncol = index.column();
	int nrow = index.row();
	QStringList values = m_tableValue.at(nrow);
	retStat = values.at(0);

	return retStat;
}

QString WatchModel::getStat(QModelIndex index)
{
	QString retStat;
	int ncol = index.column();
	int nrow = index.row();
	QStringList values = m_tableValue.at(nrow);
	retStat = values.at(2);

	return retStat;
}

//--------------------------------------------------
ButtonDelegate::ButtonDelegate(QObject * parent) : QStyledItemDelegate(parent), m_tableView(NULL)
{
	statusModify = NORMAL;
	statusDelete = NORMAL;

	this->picmodify_name = QString(":img/write");
	this->picdelete_name = QString(":img/delete");
}

void ButtonDelegate::paint(QPainter *painter, const QStyleOptionViewItem &option, const QModelIndex &index) const
{
		int colume = index.column();
		if (colume != 3)
		{
			QStyledItemDelegate::paint(painter, option, index);
			return;
		}

		QStyleOptionViewItem  viewOption(option);
		if (viewOption.state & QStyle::State_HasFocus)
		{
			viewOption.state = viewOption.state ^ QStyle::State_HasFocus;
		}

		QStyledItemDelegate::paint(painter, viewOption, index);
		//------------------modify
		QPixmap pixmapmodify;
		switch (statusModify)
		{
		case NORMAL:
		{
					   pixmapmodify.load(picmodify_name);
					   break;
		}
		case HOVER:
		{
					  pixmapmodify.load(picmodify_name + QString("_hover"));
					  break;
		}
		case PRESS:
		{
					  pixmapmodify.load(picmodify_name + QString("_hover"));
					  break;
		}
		default:
			break;
		}

		QImage imagemodify(picmodify_name);
		int imagewidthmodify = imagemodify.width();
		int imageheightmodify = imagemodify.height();
		int heightmodify = (viewOption.rect.height() - imageheightmodify) / 2;

		QRect decorationRectmodify = QRect(viewOption.rect.left(), viewOption.rect.top() + heightmodify, imagewidthmodify, imageheightmodify);
		painter->drawPixmap(decorationRectmodify, pixmapmodify);


		//------------------delete
		QPixmap pixmapdelete;
		switch (statusDelete)
		{
			case NORMAL:
			{
						   pixmapdelete.load(picdelete_name);
						   break;
			}
			case HOVER:
			{
						  pixmapdelete.load(picdelete_name + QString("_hover"));
						  break;
			}
			case PRESS:
			{
						  pixmapdelete.load(picdelete_name + QString("_hover"));
						  break;
			}
			default:
				break;
		}

		QImage imagedelete(picdelete_name);
		int imagewidthdelete = imagedelete.width();
		int imageheightdelete = imagedelete.height();
		int heightdelete = (viewOption.rect.height() - imageheightdelete) / 2;

		int deletex = viewOption.rect.left() + imagewidthmodify + 40;
		QRect decorationRectdelete = QRect(deletex, viewOption.rect.top() + heightmodify, imagewidthdelete, imageheightdelete);
		painter->drawPixmap(decorationRectdelete, pixmapdelete);
}

bool ButtonDelegate::editorEvent(QEvent *event, QAbstractItemModel *model, const QStyleOptionViewItem &option, const QModelIndex &index)
{
		int colume = index.column();
		if (colume != 3)
		{
			return QStyledItemDelegate::editorEvent(event, model, option, index);
		}
		QMouseEvent *mouseEvent = static_cast<QMouseEvent*>(event);
		//------------------modify
		QImage imagemodify(picmodify_name);
		int imagewidthmodify = imagemodify.width();
		int imageheightmodify = imagemodify.height();
		int heightmodify = (option.rect.height() - imageheightmodify) / 2;

		QRect decorationRectmodify = QRect(option.rect.left(), option.rect.top() + heightmodify, imagewidthmodify, imageheightmodify);

		if (event->type() == QEvent::MouseMove && decorationRectmodify.contains(mouseEvent->pos()))
		{
			statusModify = HOVER;
		}
		else
		{
			statusModify = NORMAL;
		}
		if (event->type() == QEvent::MouseButtonPress && decorationRectmodify.contains(mouseEvent->pos()))
		{
			statusModify = PRESS;
			emit itemClickedModify(index);
		}

		//------------------delete
		QImage imagedelete(picdelete_name);
		int imagewidthdelete = imagedelete.width();
		int imageheightdelete = imagedelete.height();
		int heightdelete = (option.rect.height() - imageheightdelete) / 2;

		int deletex = option.rect.left() + imagewidthmodify + 40;
		QRect decorationRectdelete = QRect(deletex, option.rect.top() + heightmodify, imagewidthdelete, imageheightdelete);

		if (event->type() == QEvent::MouseMove && decorationRectdelete.contains(mouseEvent->pos()))
		{
			statusDelete = HOVER;
		}
		else
		{
			statusDelete = NORMAL;
		}
		if (event->type() == QEvent::MouseButtonPress && decorationRectdelete.contains(mouseEvent->pos()))
		{
			statusDelete = PRESS;
			emit itemClickedDelete(index);
		}
		return true;
}

void ButtonDelegate::setView(QTableView *tableView)
{
	m_tableView = tableView;
}


//--------------------------------------------------
WatchView::WatchView(QWidget *parent) :QTableView(parent)
{
	m_model = new WatchModel();
	this->setModel(m_model);

	m_buttonDelegate = new ButtonDelegate(this);
	m_buttonDelegate->setView(this);
	//this->setItemDelegateForColumn(3, m_buttonDelegate);
	this->setItemDelegate(m_buttonDelegate);

	this->setFocusPolicy(Qt::NoFocus);
	this->verticalHeader()->setDefaultSectionSize(40);
	this->setFrameShape(QFrame::NoFrame);
	this->setShowGrid(false);
	this->verticalHeader()->setVisible(false);
	this->horizontalHeader()->setStretchLastSection(true);

	this->horizontalHeader()->setHighlightSections(false);
	this->horizontalHeader()->setSectionResizeMode(QHeaderView::Stretch);
	/*this->horizontalHeader()->resizeSection(0, 200);
	this->horizontalHeader()->resizeSection(1, 130);
	this->horizontalHeader()->resizeSection(2, 134);
	this->horizontalHeader()->resizeSection(3, 134);
	this->horizontalHeader()->resizeSection(4, 134);
	this->horizontalHeader()->resizeSection(5, 134);
	this->horizontalHeader()->resizeSection(6, 130);*/
	this->setSelectionBehavior(QAbstractItemView::SelectRows);
	this->setSelectionMode(QAbstractItemView::SingleSelection);


	this->setEditTriggers(QAbstractItemView::NoEditTriggers);
	this->horizontalHeader()->setFixedHeight(30);

	this->horizontalHeader()->setStyleSheet("QHeaderView::section{background:white;border:0px;color:rgb(103,106,108);}");
	this->horizontalScrollBar()->setStyleSheet("QScrollBar{background:transparent; height:10px;}"
		"QScrollBar::handle{background:lightgray; border:2px solid transparent; border-radius:5px;}"
		"QScrollBar::handle:hover{background:gray;}"
		"QScrollBar::sub-line{background:transparent;}"
		"QScrollBar::add-line{background:transparent;}");
	this->verticalScrollBar()->setStyleSheet("QScrollBar{background:transparent; width: 10px;}"
		"QScrollBar::handle{background:lightgray; border:2px solid transparent; border-radius:5px;}"
		"QScrollBar::handle:hover{background:gray;}"
		"QScrollBar::sub-line{background:transparent;}"
		"QScrollBar::add-line{background:transparent;}");
	this->setObjectName("watchview");

	this->setFixedSize(960,310);

	/*for (int i = 0; i < 100; i++)
	{
		QStringList data;
		QString id = QString::number(i+1, 10);
		data << id << QStringLiteral("顾炎武") << QStringLiteral("库存");
		m_model->setData(data);
	}*/

	//this->verticalScrollBar()->setMaximum(5);
	this->setContextMenuPolicy(Qt::NoContextMenu);
	connect(m_buttonDelegate, SIGNAL(itemClickedModify(QModelIndex)), this, SLOT(popModify(QModelIndex)));
	connect(m_buttonDelegate, SIGNAL(itemClickedDelete(QModelIndex)), this, SLOT(DealDelete(QModelIndex)));
	connect(m_model,SIGNAL(loadPage()),this,SIGNAL(loadPage()));
}

WatchView::~WatchView()
{
	delete m_model;
}

void WatchView::LoadData()
{
	m_model->LoadData();
}

int WatchView::getRowCount()
{
	return m_model->rowCount();
}

void WatchView::search(QString id, QString stat)
{
	m_model->LoadSearch(id,stat);
}

void WatchView::popModify(const QModelIndex &index)
{
	//Q_UNUSED(index);
	int stat = (m_model->getStat(index)).toInt();
	if (1 == stat)
		return;
	MainWidget* pMainWidget = (qobject_cast<MainWidget *>(parent()->parent()->parent()->parent()->parent()->parent()));
	ModifyWidget* modifyWidget = new ModifyWidget(this);
	modifyWidget->reqBycode(m_model->getCode(index));

	connect(modifyWidget, SIGNAL(CloseWidget()), modifyWidget,SLOT(close()));
	if (pMainWidget->windowState() == Qt::WindowMaximized || pMainWidget->windowState() == Qt::WindowFullScreen)
	{
		QDesktopWidget* desktop = QApplication::desktop();
		modifyWidget->move((desktop->width() - modifyWidget->width()) / 2, (desktop->height() - modifyWidget->height()) / 2);
		modifyWidget->exec();
	}
	else
	{
		QPoint pos;
		pos.setX((pMainWidget->width() - modifyWidget->width()) / 2);
		pos.setY((pMainWidget->height() - modifyWidget->height()) / 2);
		modifyWidget->move(pMainWidget->mapToGlobal(pos));
		modifyWidget->exec();
	}
}

void WatchView::popAdd(QString data)
{
	m_watchID = data;
	MainWidget* pMainWidget = (qobject_cast<MainWidget *>(parent()->parent()->parent()->parent()->parent()->parent()));
	ModifyWidget* modifyWidget = new ModifyWidget(this);
	modifyWidget->setTagName(QStringLiteral("添加"));
	modifyWidget->setAddWatch(data);
	modifyWidget->setStatIndex(0);
	modifyWidget->setOperateStat(1);//为添加操作

	connect(modifyWidget, SIGNAL(CloseWidget()), modifyWidget, SLOT(close()));
	if (pMainWidget->windowState() == Qt::WindowMaximized || pMainWidget->windowState() == Qt::WindowFullScreen)
	{
		QDesktopWidget* desktop = QApplication::desktop();
		modifyWidget->move((desktop->width() - modifyWidget->width()) / 2, (desktop->height() - modifyWidget->height()) / 2);
		modifyWidget->exec();
	}
	else
	{
		QPoint pos;
		pos.setX((pMainWidget->width() - modifyWidget->width()) / 2);
		pos.setY((pMainWidget->height() - modifyWidget->height()) / 2);
		modifyWidget->move(pMainWidget->mapToGlobal(pos));
		modifyWidget->exec();
	}
}

void WatchView::DealDelete(const QModelIndex &index)
{
	int stat = (m_model->getStat(index)).toInt();
	if (1 == stat)
		return;

	m_model->deleteRow(index);
}