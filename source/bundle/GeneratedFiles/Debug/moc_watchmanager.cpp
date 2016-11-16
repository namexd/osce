/****************************************************************************
** Meta object code from reading C++ file 'watchmanager.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/watchmanager/watchmanager.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'watchmanager.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_WatchManagerTopWidget_t {
    QByteArrayData data[5];
    char stringdata0[71];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WatchManagerTopWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WatchManagerTopWidget_t qt_meta_stringdata_WatchManagerTopWidget = {
    {
QT_MOC_LITERAL(0, 0, 21), // "WatchManagerTopWidget"
QT_MOC_LITERAL(1, 22, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 42, 0), // ""
QT_MOC_LITERAL(3, 43, 8), // "curIndex"
QT_MOC_LITERAL(4, 52, 18) // "setMainContentPage"

    },
    "WatchManagerTopWidget\0turnMainContentPage\0"
    "\0curIndex\0setMainContentPage"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WatchManagerTopWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       2,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       1,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   24,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       4,    0,   27,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,

 // slots: parameters
    QMetaType::Void,

       0        // eod
};

void WatchManagerTopWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        WatchManagerTopWidget *_t = static_cast<WatchManagerTopWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 1: _t->setMainContentPage(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (WatchManagerTopWidget::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&WatchManagerTopWidget::turnMainContentPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject WatchManagerTopWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_WatchManagerTopWidget.data,
      qt_meta_data_WatchManagerTopWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WatchManagerTopWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WatchManagerTopWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WatchManagerTopWidget.stringdata0))
        return static_cast<void*>(const_cast< WatchManagerTopWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int WatchManagerTopWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
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
void WatchManagerTopWidget::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
struct qt_meta_stringdata_WatchContentSearch_t {
    QByteArrayData data[8];
    char stringdata0[60];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WatchContentSearch_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WatchContentSearch_t qt_meta_stringdata_WatchContentSearch = {
    {
QT_MOC_LITERAL(0, 0, 18), // "WatchContentSearch"
QT_MOC_LITERAL(1, 19, 6), // "search"
QT_MOC_LITERAL(2, 26, 0), // ""
QT_MOC_LITERAL(3, 27, 2), // "id"
QT_MOC_LITERAL(4, 30, 4), // "stat"
QT_MOC_LITERAL(5, 35, 10), // "dealSearch"
QT_MOC_LITERAL(6, 46, 8), // "setIndex"
QT_MOC_LITERAL(7, 55, 4) // "data"

    },
    "WatchContentSearch\0search\0\0id\0stat\0"
    "dealSearch\0setIndex\0data"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WatchContentSearch[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       3,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       1,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    2,   29,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       5,    0,   34,    2, 0x0a /* Public */,
       6,    1,   35,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::QString, QMetaType::QString,    3,    4,

 // slots: parameters
    QMetaType::Void,
    QMetaType::Void, QMetaType::Int,    7,

       0        // eod
};

void WatchContentSearch::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        WatchContentSearch *_t = static_cast<WatchContentSearch *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->search((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 1: _t->dealSearch(); break;
        case 2: _t->setIndex((*reinterpret_cast< int(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (WatchContentSearch::*_t)(QString , QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&WatchContentSearch::search)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject WatchContentSearch::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_WatchContentSearch.data,
      qt_meta_data_WatchContentSearch,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WatchContentSearch::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WatchContentSearch::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WatchContentSearch.stringdata0))
        return static_cast<void*>(const_cast< WatchContentSearch*>(this));
    return QWidget::qt_metacast(_clname);
}

int WatchContentSearch::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 3)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 3;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 3)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 3;
    }
    return _id;
}

// SIGNAL 0
void WatchContentSearch::search(QString _t1, QString _t2)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)), const_cast<void*>(reinterpret_cast<const void*>(&_t2)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
struct qt_meta_stringdata_WatchManagerContentWidget_t {
    QByteArrayData data[1];
    char stringdata0[26];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WatchManagerContentWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WatchManagerContentWidget_t qt_meta_stringdata_WatchManagerContentWidget = {
    {
QT_MOC_LITERAL(0, 0, 25) // "WatchManagerContentWidget"

    },
    "WatchManagerContentWidget"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WatchManagerContentWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       0,    0, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       0,       // signalCount

       0        // eod
};

void WatchManagerContentWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    Q_UNUSED(_o);
    Q_UNUSED(_id);
    Q_UNUSED(_c);
    Q_UNUSED(_a);
}

const QMetaObject WatchManagerContentWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_WatchManagerContentWidget.data,
      qt_meta_data_WatchManagerContentWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WatchManagerContentWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WatchManagerContentWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WatchManagerContentWidget.stringdata0))
        return static_cast<void*>(const_cast< WatchManagerContentWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int WatchManagerContentWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    return _id;
}
struct qt_meta_stringdata_WatchManagerWidget_t {
    QByteArrayData data[4];
    char stringdata0[49];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WatchManagerWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WatchManagerWidget_t qt_meta_stringdata_WatchManagerWidget = {
    {
QT_MOC_LITERAL(0, 0, 18), // "WatchManagerWidget"
QT_MOC_LITERAL(1, 19, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 39, 0), // ""
QT_MOC_LITERAL(3, 40, 8) // "curIndex"

    },
    "WatchManagerWidget\0turnMainContentPage\0"
    "\0curIndex"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WatchManagerWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       1,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       1,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   19,    2, 0x06 /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,

       0        // eod
};

void WatchManagerWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        WatchManagerWidget *_t = static_cast<WatchManagerWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (WatchManagerWidget::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&WatchManagerWidget::turnMainContentPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject WatchManagerWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_WatchManagerWidget.data,
      qt_meta_data_WatchManagerWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WatchManagerWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WatchManagerWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WatchManagerWidget.stringdata0))
        return static_cast<void*>(const_cast< WatchManagerWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int WatchManagerWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 1)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 1;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 1)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 1;
    }
    return _id;
}

// SIGNAL 0
void WatchManagerWidget::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
struct qt_meta_stringdata_WatchManager_t {
    QByteArrayData data[8];
    char stringdata0[76];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WatchManager_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WatchManager_t qt_meta_stringdata_WatchManager = {
    {
QT_MOC_LITERAL(0, 0, 12), // "WatchManager"
QT_MOC_LITERAL(1, 13, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 33, 0), // ""
QT_MOC_LITERAL(3, 34, 8), // "curIndex"
QT_MOC_LITERAL(4, 43, 11), // "SigAddWatch"
QT_MOC_LITERAL(5, 55, 4), // "data"
QT_MOC_LITERAL(6, 60, 10), // "slotNoExam"
QT_MOC_LITERAL(7, 71, 4) // "name"

    },
    "WatchManager\0turnMainContentPage\0\0"
    "curIndex\0SigAddWatch\0data\0slotNoExam\0"
    "name"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WatchManager[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       3,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       1,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   29,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       4,    1,   32,    2, 0x0a /* Public */,
       6,    1,   35,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,

 // slots: parameters
    QMetaType::Void, QMetaType::QString,    5,
    QMetaType::Void, QMetaType::QString,    7,

       0        // eod
};

void WatchManager::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        WatchManager *_t = static_cast<WatchManager *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 1: _t->SigAddWatch((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 2: _t->slotNoExam((*reinterpret_cast< QString(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (WatchManager::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&WatchManager::turnMainContentPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject WatchManager::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_WatchManager.data,
      qt_meta_data_WatchManager,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WatchManager::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WatchManager::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WatchManager.stringdata0))
        return static_cast<void*>(const_cast< WatchManager*>(this));
    return QWidget::qt_metacast(_clname);
}

int WatchManager::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 3)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 3;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 3)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 3;
    }
    return _id;
}

// SIGNAL 0
void WatchManager::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
QT_END_MOC_NAMESPACE
