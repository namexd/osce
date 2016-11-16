/****************************************************************************
** Meta object code from reading C++ file 'watchview.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/watchmanager/watchview.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'watchview.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_WatchModel_t {
    QByteArrayData data[9];
    char stringdata0[73];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WatchModel_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WatchModel_t qt_meta_stringdata_WatchModel = {
    {
QT_MOC_LITERAL(0, 0, 10), // "WatchModel"
QT_MOC_LITERAL(1, 11, 8), // "loadPage"
QT_MOC_LITERAL(2, 20, 0), // ""
QT_MOC_LITERAL(3, 21, 14), // "recevieRequest"
QT_MOC_LITERAL(4, 36, 4), // "stat"
QT_MOC_LITERAL(5, 41, 4), // "data"
QT_MOC_LITERAL(6, 46, 4), // "type"
QT_MOC_LITERAL(7, 51, 10), // "recevieALL"
QT_MOC_LITERAL(8, 62, 10) // "recevieDel"

    },
    "WatchModel\0loadPage\0\0recevieRequest\0"
    "stat\0data\0type\0recevieALL\0recevieDel"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WatchModel[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       4,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       1,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    0,   34,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       3,    3,   35,    2, 0x0a /* Public */,
       7,    2,   42,    2, 0x0a /* Public */,
       8,    2,   47,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void,

 // slots: parameters
    QMetaType::Void, QMetaType::Int, QMetaType::QString, QMetaType::Int,    4,    5,    6,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,    4,    5,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,    4,    5,

       0        // eod
};

void WatchModel::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        WatchModel *_t = static_cast<WatchModel *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->loadPage(); break;
        case 1: _t->recevieRequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2])),(*reinterpret_cast< int(*)>(_a[3]))); break;
        case 2: _t->recevieALL((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 3: _t->recevieDel((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (WatchModel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&WatchModel::loadPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject WatchModel::staticMetaObject = {
    { &QAbstractTableModel::staticMetaObject, qt_meta_stringdata_WatchModel.data,
      qt_meta_data_WatchModel,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WatchModel::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WatchModel::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WatchModel.stringdata0))
        return static_cast<void*>(const_cast< WatchModel*>(this));
    return QAbstractTableModel::qt_metacast(_clname);
}

int WatchModel::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QAbstractTableModel::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 4)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 4;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 4)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 4;
    }
    return _id;
}

// SIGNAL 0
void WatchModel::loadPage()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}
struct qt_meta_stringdata_ButtonDelegate_t {
    QByteArrayData data[5];
    char stringdata0[58];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ButtonDelegate_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ButtonDelegate_t qt_meta_stringdata_ButtonDelegate = {
    {
QT_MOC_LITERAL(0, 0, 14), // "ButtonDelegate"
QT_MOC_LITERAL(1, 15, 17), // "itemClickedModify"
QT_MOC_LITERAL(2, 33, 0), // ""
QT_MOC_LITERAL(3, 34, 5), // "index"
QT_MOC_LITERAL(4, 40, 17) // "itemClickedDelete"

    },
    "ButtonDelegate\0itemClickedModify\0\0"
    "index\0itemClickedDelete"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ButtonDelegate[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       2,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       2,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   24,    2, 0x06 /* Public */,
       4,    1,   27,    2, 0x06 /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::QModelIndex,    3,
    QMetaType::Void, QMetaType::QModelIndex,    3,

       0        // eod
};

void ButtonDelegate::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ButtonDelegate *_t = static_cast<ButtonDelegate *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->itemClickedModify((*reinterpret_cast< const QModelIndex(*)>(_a[1]))); break;
        case 1: _t->itemClickedDelete((*reinterpret_cast< const QModelIndex(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ButtonDelegate::*_t)(const QModelIndex & );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ButtonDelegate::itemClickedModify)) {
                *result = 0;
            }
        }
        {
            typedef void (ButtonDelegate::*_t)(const QModelIndex & );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ButtonDelegate::itemClickedDelete)) {
                *result = 1;
            }
        }
    }
}

const QMetaObject ButtonDelegate::staticMetaObject = {
    { &QStyledItemDelegate::staticMetaObject, qt_meta_stringdata_ButtonDelegate.data,
      qt_meta_data_ButtonDelegate,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ButtonDelegate::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ButtonDelegate::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ButtonDelegate.stringdata0))
        return static_cast<void*>(const_cast< ButtonDelegate*>(this));
    return QStyledItemDelegate::qt_metacast(_clname);
}

int ButtonDelegate::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QStyledItemDelegate::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 2)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 2;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 2)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 2;
    }
    return _id;
}

// SIGNAL 0
void ButtonDelegate::itemClickedModify(const QModelIndex & _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void ButtonDelegate::itemClickedDelete(const QModelIndex & _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 1, _a);
}
struct qt_meta_stringdata_WatchView_t {
    QByteArrayData data[11];
    char stringdata0[74];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WatchView_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WatchView_t qt_meta_stringdata_WatchView = {
    {
QT_MOC_LITERAL(0, 0, 9), // "WatchView"
QT_MOC_LITERAL(1, 10, 8), // "loadPage"
QT_MOC_LITERAL(2, 19, 0), // ""
QT_MOC_LITERAL(3, 20, 9), // "popModify"
QT_MOC_LITERAL(4, 30, 5), // "index"
QT_MOC_LITERAL(5, 36, 6), // "popAdd"
QT_MOC_LITERAL(6, 43, 4), // "data"
QT_MOC_LITERAL(7, 48, 6), // "search"
QT_MOC_LITERAL(8, 55, 2), // "id"
QT_MOC_LITERAL(9, 58, 4), // "stat"
QT_MOC_LITERAL(10, 63, 10) // "DealDelete"

    },
    "WatchView\0loadPage\0\0popModify\0index\0"
    "popAdd\0data\0search\0id\0stat\0DealDelete"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WatchView[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       5,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       1,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    0,   39,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       3,    1,   40,    2, 0x0a /* Public */,
       5,    1,   43,    2, 0x0a /* Public */,
       7,    2,   46,    2, 0x0a /* Public */,
      10,    1,   51,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void,

 // slots: parameters
    QMetaType::Void, QMetaType::QModelIndex,    4,
    QMetaType::Void, QMetaType::QString,    6,
    QMetaType::Void, QMetaType::QString, QMetaType::QString,    8,    9,
    QMetaType::Void, QMetaType::QModelIndex,    4,

       0        // eod
};

void WatchView::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        WatchView *_t = static_cast<WatchView *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->loadPage(); break;
        case 1: _t->popModify((*reinterpret_cast< const QModelIndex(*)>(_a[1]))); break;
        case 2: _t->popAdd((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 3: _t->search((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 4: _t->DealDelete((*reinterpret_cast< const QModelIndex(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (WatchView::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&WatchView::loadPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject WatchView::staticMetaObject = {
    { &QTableView::staticMetaObject, qt_meta_stringdata_WatchView.data,
      qt_meta_data_WatchView,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WatchView::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WatchView::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WatchView.stringdata0))
        return static_cast<void*>(const_cast< WatchView*>(this));
    return QTableView::qt_metacast(_clname);
}

int WatchView::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QTableView::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 5)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 5;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 5)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 5;
    }
    return _id;
}

// SIGNAL 0
void WatchView::loadPage()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}
QT_END_MOC_NAMESPACE
