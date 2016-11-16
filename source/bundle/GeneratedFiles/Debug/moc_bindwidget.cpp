/****************************************************************************
** Meta object code from reading C++ file 'bindwidget.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/bind/bindwidget.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'bindwidget.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_CommentBindTopWidget_t {
    QByteArrayData data[1];
    char stringdata0[21];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_CommentBindTopWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_CommentBindTopWidget_t qt_meta_stringdata_CommentBindTopWidget = {
    {
QT_MOC_LITERAL(0, 0, 20) // "CommentBindTopWidget"

    },
    "CommentBindTopWidget"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_CommentBindTopWidget[] = {

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

void CommentBindTopWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    Q_UNUSED(_o);
    Q_UNUSED(_id);
    Q_UNUSED(_c);
    Q_UNUSED(_a);
}

const QMetaObject CommentBindTopWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_CommentBindTopWidget.data,
      qt_meta_data_CommentBindTopWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *CommentBindTopWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *CommentBindTopWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_CommentBindTopWidget.stringdata0))
        return static_cast<void*>(const_cast< CommentBindTopWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int CommentBindTopWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    return _id;
}
struct qt_meta_stringdata_StudentInfoWidget_t {
    QByteArrayData data[7];
    char stringdata0[61];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_StudentInfoWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_StudentInfoWidget_t qt_meta_stringdata_StudentInfoWidget = {
    {
QT_MOC_LITERAL(0, 0, 17), // "StudentInfoWidget"
QT_MOC_LITERAL(1, 18, 13), // "setIDCardData"
QT_MOC_LITERAL(2, 32, 0), // ""
QT_MOC_LITERAL(3, 33, 7), // "stuName"
QT_MOC_LITERAL(4, 41, 5), // "stuNO"
QT_MOC_LITERAL(5, 47, 4), // "idNO"
QT_MOC_LITERAL(6, 52, 8) // "ticketNO"

    },
    "StudentInfoWidget\0setIDCardData\0\0"
    "stuName\0stuNO\0idNO\0ticketNO"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_StudentInfoWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       1,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       0,       // signalCount

 // slots: name, argc, parameters, tag, flags
       1,    4,   19,    2, 0x0a /* Public */,

 // slots: parameters
    QMetaType::Void, QMetaType::QString, QMetaType::QString, QMetaType::QString, QMetaType::QString,    3,    4,    5,    6,

       0        // eod
};

void StudentInfoWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        StudentInfoWidget *_t = static_cast<StudentInfoWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->setIDCardData((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2])),(*reinterpret_cast< QString(*)>(_a[3])),(*reinterpret_cast< QString(*)>(_a[4]))); break;
        default: ;
        }
    }
}

const QMetaObject StudentInfoWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_StudentInfoWidget.data,
      qt_meta_data_StudentInfoWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *StudentInfoWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *StudentInfoWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_StudentInfoWidget.stringdata0))
        return static_cast<void*>(const_cast< StudentInfoWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int StudentInfoWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
struct qt_meta_stringdata_WristWatchInfoWidget_t {
    QByteArrayData data[5];
    char stringdata0[47];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_WristWatchInfoWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_WristWatchInfoWidget_t qt_meta_stringdata_WristWatchInfoWidget = {
    {
QT_MOC_LITERAL(0, 0, 20), // "WristWatchInfoWidget"
QT_MOC_LITERAL(1, 21, 16), // "setSmartCardData"
QT_MOC_LITERAL(2, 38, 0), // ""
QT_MOC_LITERAL(3, 39, 2), // "NO"
QT_MOC_LITERAL(4, 42, 4) // "Stat"

    },
    "WristWatchInfoWidget\0setSmartCardData\0"
    "\0NO\0Stat"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_WristWatchInfoWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       1,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       0,       // signalCount

 // slots: name, argc, parameters, tag, flags
       1,    2,   19,    2, 0x0a /* Public */,

 // slots: parameters
    QMetaType::Void, QMetaType::QString, QMetaType::QString,    3,    4,

       0        // eod
};

void WristWatchInfoWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        WristWatchInfoWidget *_t = static_cast<WristWatchInfoWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->setSmartCardData((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        default: ;
        }
    }
}

const QMetaObject WristWatchInfoWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_WristWatchInfoWidget.data,
      qt_meta_data_WristWatchInfoWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *WristWatchInfoWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *WristWatchInfoWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_WristWatchInfoWidget.stringdata0))
        return static_cast<void*>(const_cast< WristWatchInfoWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int WristWatchInfoWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
struct qt_meta_stringdata_BindWidget_t {
    QByteArrayData data[23];
    char stringdata0[213];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_BindWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_BindWidget_t qt_meta_stringdata_BindWidget = {
    {
QT_MOC_LITERAL(0, 0, 10), // "BindWidget"
QT_MOC_LITERAL(1, 11, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 31, 0), // ""
QT_MOC_LITERAL(3, 32, 8), // "curIndex"
QT_MOC_LITERAL(4, 41, 11), // "SigAddWatch"
QT_MOC_LITERAL(5, 53, 4), // "data"
QT_MOC_LITERAL(6, 58, 8), // "turnPage"
QT_MOC_LITERAL(7, 67, 18), // "setMainContentPage"
QT_MOC_LITERAL(8, 86, 7), // "getExam"
QT_MOC_LITERAL(9, 94, 4), // "name"
QT_MOC_LITERAL(10, 99, 6), // "popTip"
QT_MOC_LITERAL(11, 106, 3), // "tip"
QT_MOC_LITERAL(12, 110, 8), // "popWatch"
QT_MOC_LITERAL(13, 119, 7), // "popBind"
QT_MOC_LITERAL(14, 127, 15), // "cacheIDCardData"
QT_MOC_LITERAL(15, 143, 7), // "stuName"
QT_MOC_LITERAL(16, 151, 5), // "stuNO"
QT_MOC_LITERAL(17, 157, 4), // "idNO"
QT_MOC_LITERAL(18, 162, 8), // "ticketNO"
QT_MOC_LITERAL(19, 171, 18), // "cacheSmartCardData"
QT_MOC_LITERAL(20, 190, 2), // "NO"
QT_MOC_LITERAL(21, 193, 4), // "Stat"
QT_MOC_LITERAL(22, 198, 14) // "changeAddWatch"

    },
    "BindWidget\0turnMainContentPage\0\0"
    "curIndex\0SigAddWatch\0data\0turnPage\0"
    "setMainContentPage\0getExam\0name\0popTip\0"
    "tip\0popWatch\0popBind\0cacheIDCardData\0"
    "stuName\0stuNO\0idNO\0ticketNO\0"
    "cacheSmartCardData\0NO\0Stat\0changeAddWatch"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_BindWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
      11,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       2,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   69,    2, 0x06 /* Public */,
       4,    1,   72,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       6,    1,   75,    2, 0x0a /* Public */,
       7,    0,   78,    2, 0x0a /* Public */,
       8,    1,   79,    2, 0x0a /* Public */,
      10,    1,   82,    2, 0x0a /* Public */,
      12,    1,   85,    2, 0x0a /* Public */,
      13,    1,   88,    2, 0x0a /* Public */,
      14,    4,   91,    2, 0x0a /* Public */,
      19,    2,  100,    2, 0x0a /* Public */,
      22,    1,  105,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,
    QMetaType::Void, QMetaType::QString,    5,

 // slots: parameters
    QMetaType::Void, QMetaType::Int,    3,
    QMetaType::Void,
    QMetaType::Void, QMetaType::QString,    9,
    QMetaType::Void, QMetaType::QString,   11,
    QMetaType::Void, QMetaType::QString,   11,
    QMetaType::Void, QMetaType::QString,   11,
    QMetaType::Void, QMetaType::QString, QMetaType::QString, QMetaType::QString, QMetaType::QString,   15,   16,   17,   18,
    QMetaType::Void, QMetaType::QString, QMetaType::QString,   20,   21,
    QMetaType::Void, QMetaType::QString,    5,

       0        // eod
};

void BindWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        BindWidget *_t = static_cast<BindWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 1: _t->SigAddWatch((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 2: _t->turnPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 3: _t->setMainContentPage(); break;
        case 4: _t->getExam((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 5: _t->popTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 6: _t->popWatch((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 7: _t->popBind((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 8: _t->cacheIDCardData((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2])),(*reinterpret_cast< QString(*)>(_a[3])),(*reinterpret_cast< QString(*)>(_a[4]))); break;
        case 9: _t->cacheSmartCardData((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 10: _t->changeAddWatch((*reinterpret_cast< QString(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (BindWidget::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&BindWidget::turnMainContentPage)) {
                *result = 0;
            }
        }
        {
            typedef void (BindWidget::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&BindWidget::SigAddWatch)) {
                *result = 1;
            }
        }
    }
}

const QMetaObject BindWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_BindWidget.data,
      qt_meta_data_BindWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *BindWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *BindWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_BindWidget.stringdata0))
        return static_cast<void*>(const_cast< BindWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int BindWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 11)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 11;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 11)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 11;
    }
    return _id;
}

// SIGNAL 0
void BindWidget::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void BindWidget::SigAddWatch(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 1, _a);
}
QT_END_MOC_NAMESPACE
