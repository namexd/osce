#ifndef EXAMLIST
#define EXAMLIST

#include <QAbstractTableModel>
#include <QStringList>
#include <QList>
#include <QItemSelectionModel>
#include <QTableView>
#include <QMouseEvent>
#include <QHeaderView>

class ExamModel : public QAbstractTableModel
{
	Q_OBJECT

public:
	ExamModel(QObject *parent = 0);
	~ExamModel();

	int rowCount(const QModelIndex &parent) const;
	int columnCount(const QModelIndex &parent) const;
	QVariant data(const QModelIndex &index, int role) const;
	QVariant headerData(int section, Qt::Orientation orientation, int role = Qt::DisplayRole) const;
	Qt::ItemFlags flags(const QModelIndex &index) const;

	void setData(QStringList data);


	void Load(QStringList ids, QStringList names);


	QString getClickID(QModelIndex index);
	QString getClickName(QModelIndex index);
private:
	QStringList m_data;
	QStringList m_ids;
};


class ExamView : public QTableView
{
	Q_OBJECT

public:
	ExamView(QWidget *parent = 0);
	~ExamView();
	void setModeData(QList<int> stats);
	int retOrderStat();
	void setExamViewData(QStringList ids, QStringList names);
signals:
	void turnMainContentPage(int curIndex);
public slots:
	void DealClick(const QModelIndex &index);
private:
	ExamModel* m_model;
};

#endif