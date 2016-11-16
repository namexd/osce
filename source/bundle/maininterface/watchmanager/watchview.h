#ifndef WATCHVIEW
#define WATCHVIEW

#include <QObject>
#include <QTableView>
#include <QHeaderView>
#include <QScrollBar>
#include <QItemDelegate>
#include <QAbstractTableModel>
#include <QStyledItemDelegate>

class WatchModel :public QAbstractTableModel
{
	Q_OBJECT
public:
	explicit WatchModel(QObject* parent=0);
	~WatchModel();

	void setHorizontalHeaderList(QStringList horizontalHeaderList);
	int rowCount(const QModelIndex &parent = QModelIndex()) const;
	int columnCount(const QModelIndex &parent = QModelIndex()) const;
	QVariant data(const QModelIndex &index, int role) const;
	QVariant headerData(int section, Qt::Orientation orientation, int role) const;
	Qt::ItemFlags flags(const QModelIndex &index) const;


	void setData(QStringList  data);
	//ÇëÇóÊý¾Ý
	void LoadData();
	void LoadSearch(QString id, QString stat);

	//
	QString getWatchStat(QString st) const;

	QString getCode(QModelIndex index);

	QString getStat(QModelIndex index);

	//
	void deleteRow(QModelIndex index);
signals:
	void loadPage();
public slots:
	void recevieRequest(int stat, QString data, int type);
	void recevieALL(int stat, QString data);
	void recevieDel(int stat, QString data);
private:
	QVector<QStringList> m_tableValue;
	QStringList m_horizontalHeaderList;
};

//--------------------------------------------------
enum Status{ NORMAL, HOVER, PRESS };
class ButtonDelegate :public QStyledItemDelegate
{
	Q_OBJECT
public:
	ButtonDelegate(QObject * parent = 0);
	virtual ~ButtonDelegate(){}
	void paint(QPainter * painter, const QStyleOptionViewItem & option, const QModelIndex & index) const;
	bool editorEvent(QEvent *event, QAbstractItemModel *model, const QStyleOptionViewItem &option, const QModelIndex &index);
	void setView(QTableView *tableView);
signals:
	void itemClickedModify(const QModelIndex &index);
	void itemClickedDelete(const QModelIndex &index);

private:
	QTableView *m_tableView;

	Status statusModify;
	Status statusDelete;
	QString picmodify_name;
	QString picdelete_name;
};

//--------------------------------------------------
class WatchView : public QTableView
{
	Q_OBJECT
public:
	explicit WatchView(QWidget *parent = 0);
	WatchModel* tableModel() { return m_model; }
	~WatchView();

	int getRowCount();
	void LoadData();
signals:
	void loadPage();
public slots:
	void popModify(const QModelIndex &index);
	void popAdd(QString data);
	void search(QString id, QString stat);

	void DealDelete(const QModelIndex &index);
private:
	WatchModel *m_model;
	ButtonDelegate *m_buttonDelegate;


	QString m_watchID;
};

#endif