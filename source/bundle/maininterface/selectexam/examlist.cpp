#include "examlist.h"
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QFormLayout>
#include <QScrollBar>
#include "../httpapi/httprequest.h"
extern HttpRequest request;


ExamModel::ExamModel(QObject *parent)
: QAbstractTableModel(parent)
{

}

ExamModel::~ExamModel()
{

}


QVariant ExamModel::data(const QModelIndex &index, int role) const
{
	if (!index.isValid())
		return QVariant();
	if (role == Qt::DisplayRole)
	{
		//QString m_show = getOrderStat(m_data.at(index.row()));
		//return QVariant(m_show);
		return m_data.at(index.row());
	}

	if (role == Qt::SizeHintRole)
	{
		return QSize(100, 32);
	}
	if (role == Qt::TextAlignmentRole)
	{
		return int(Qt::AlignLeft | Qt::AlignVCenter | Qt::AlignHCenter);
	}
	if (role == Qt::FontRole)
	{
		QFont ft;
		ft.setFamily(trUtf8("Î¢ÈíÑÅºÚ"));
		ft.setPointSize(10);
		return QFont(ft);
	}

	return QVariant();
}

int ExamModel::rowCount(const QModelIndex &parent) const
{
	Q_UNUSED(parent);
	return m_data.size();
}

int ExamModel::columnCount(const QModelIndex &parent) const
{
	Q_UNUSED(parent);
	return 1;
}

QVariant ExamModel::headerData(int section, Qt::Orientation orientation, int role) const
{
	if (role == Qt::DisplayRole)
		return QString::number(section);
	return QAbstractItemModel::headerData(section, orientation, role);
}

Qt::ItemFlags ExamModel::flags(const QModelIndex &index) const
{
	if (!index.isValid())
		return 0;

	return   QAbstractItemModel::flags(index) | Qt::ItemIsEnabled | Qt::ItemIsSelectable;
}

void ExamModel::setData(QStringList data)
{
	m_data = data;
	emit this->layoutChanged();
}

void ExamModel::Load(QStringList ids, QStringList names)
{
	m_data.clear();
	m_data = names;
	m_ids.clear();
	m_ids = ids;
	emit this->layoutChanged();
}

QString ExamModel::getClickID(QModelIndex index)
{
	QString retStat;
	int nrow = index.row();
	retStat = m_ids.at(nrow);
	return retStat;
}

QString ExamModel::getClickName(QModelIndex index)
{
	QString retStat;
	int nrow = index.row();
	retStat = m_data.at(nrow);
	return retStat;
}
//--------------------

ExamView::ExamView(QWidget *parent)
:QTableView(parent)
{
	this->setFixedSize(280,340);
	m_model = new ExamModel(this);
	this->setContentsMargins(0, 0, 0, 0);
	this->setModel(m_model);

	this->horizontalHeader()->setStretchLastSection(true);
	this->horizontalHeader()->setHighlightSections(false);
	this->setFrameShape(QFrame::NoFrame);
	this->verticalHeader()->setVisible(false);
	this->horizontalHeader()->setVisible(false);
	this->resizeColumnsToContents();
	this->resizeRowsToContents();
	this->setSelectionBehavior(QAbstractItemView::SelectRows);
	this->setSelectionMode(QAbstractItemView::SingleSelection);
	this->setMouseTracking(true);
	this->setShowGrid(false);
	this->setFocusPolicy(Qt::NoFocus);
	//this->setStyleSheet("background-color:rgb(247,247,247);");
	this->setObjectName("groupview");

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


	this->setContextMenuPolicy(Qt::NoContextMenu);

	/*QStringList item;
	for (int i = 0; i < 100; i++)
	{
		item.append(QString::number(i+1,10));
	}
	m_model->setData(item);*/
	connect(this, SIGNAL(doubleClicked(QModelIndex)), SLOT(DealClick(QModelIndex)));
}

ExamView::~ExamView()
{

}

void ExamView::setExamViewData(QStringList ids, QStringList names)
{
	m_model->Load(ids,names);
}

void ExamView::DealClick(const QModelIndex &index)
{
	request.setExamID(m_model->getClickID(index),m_model->getClickName(index));
	emit turnMainContentPage(0);
}