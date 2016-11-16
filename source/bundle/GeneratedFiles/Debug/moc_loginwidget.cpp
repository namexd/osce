/****************************************************************************
** Meta object code from reading C++ file 'loginwidget.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/login/loginwidget.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'loginwidget.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_LoginContentWidget_t {
    QByteArrayData data[19];
    char stringdata0[184];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_LoginContentWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_LoginContentWidget_t qt_meta_stringdata_LoginContentWidget = {
    {
QT_MOC_LITERAL(0, 0, 18), // "LoginContentWidget"
QT_MOC_LITERAL(1, 19, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 39, 0), // ""
QT_MOC_LITERAL(3, 40, 8), // "curIndex"
QT_MOC_LITERAL(4, 49, 15), // "setExamViewData"
QT_MOC_LITERAL(5, 65, 3), // "ids"
QT_MOC_LITERAL(6, 69, 5), // "names"
QT_MOC_LITERAL(7, 75, 5), // "setip"
QT_MOC_LITERAL(8, 81, 4), // "name"
QT_MOC_LITERAL(9, 86, 9), // "sigNoExam"
QT_MOC_LITERAL(10, 96, 13), // "changeRemeber"
QT_MOC_LITERAL(11, 110, 5), // "login"
QT_MOC_LITERAL(12, 116, 14), // "requstExamList"
QT_MOC_LITERAL(13, 131, 14), // "recevieRequest"
QT_MOC_LITERAL(14, 146, 4), // "stat"
QT_MOC_LITERAL(15, 151, 4), // "data"
QT_MOC_LITERAL(16, 156, 4), // "type"
QT_MOC_LITERAL(17, 161, 9), // "DealLogin"
QT_MOC_LITERAL(18, 171, 12) // "DealExamList"

    },
    "LoginContentWidget\0turnMainContentPage\0"
    "\0curIndex\0setExamViewData\0ids\0names\0"
    "setip\0name\0sigNoExam\0changeRemeber\0"
    "login\0requstExamList\0recevieRequest\0"
    "stat\0data\0type\0DealLogin\0DealExamList"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_LoginContentWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
      10,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       4,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   64,    2, 0x06 /* Public */,
       4,    2,   67,    2, 0x06 /* Public */,
       7,    1,   72,    2, 0x06 /* Public */,
       9,    1,   75,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
      10,    0,   78,    2, 0x0a /* Public */,
      11,    0,   79,    2, 0x0a /* Public */,
      12,    0,   80,    2, 0x0a /* Public */,
      13,    3,   81,    2, 0x0a /* Public */,
      17,    2,   88,    2, 0x0a /* Public */,
      18,    2,   93,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,
    QMetaType::Void, QMetaType::QStringList, QMetaType::QStringList,    5,    6,
    QMetaType::Void, QMetaType::QString,    8,
    QMetaType::Void, QMetaType::QString,    8,

 // slots: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void, QMetaType::Int, QMetaType::QString, QMetaType::Int,   14,   15,   16,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,   14,   15,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,   14,   15,

       0        // eod
};

void LoginContentWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        LoginContentWidget *_t = static_cast<LoginContentWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 1: _t->setExamViewData((*reinterpret_cast< QStringList(*)>(_a[1])),(*reinterpret_cast< QStringList(*)>(_a[2]))); break;
        case 2: _t->setip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 3: _t->sigNoExam((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 4: _t->changeRemeber(); break;
        case 5: _t->login(); break;
        case 6: _t->requstExamList(); break;
        case 7: _t->recevieRequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2])),(*reinterpret_cast< int(*)>(_a[3]))); break;
        case 8: _t->DealLogin((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 9: _t->DealExamList((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (LoginContentWidget::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&LoginContentWidget::turnMainContentPage)) {
                *result = 0;
            }
        }
        {
            typedef void (LoginContentWidget::*_t)(QStringList , QStringList );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&LoginContentWidget::setExamViewData)) {
                *result = 1;
            }
        }
        {
            typedef void (LoginContentWidget::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&LoginContentWidget::setip)) {
                *result = 2;
            }
        }
        {
            typedef void (LoginContentWidget::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&LoginContentWidget::sigNoExam)) {
                *result = 3;
            }
        }
    }
}

const QMetaObject LoginContentWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_LoginContentWidget.data,
      qt_meta_data_LoginContentWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *LoginContentWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *LoginContentWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_LoginContentWidget.stringdata0))
        return static_cast<void*>(const_cast< LoginContentWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int LoginContentWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 10)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 10;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 10)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 10;
    }
    return _id;
}

// SIGNAL 0
void LoginContentWidget::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void LoginContentWidget::setExamViewData(QStringList _t1, QStringList _t2)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)), const_cast<void*>(reinterpret_cast<const void*>(&_t2)) };
    QMetaObject::activate(this, &staticMetaObject, 1, _a);
}

// SIGNAL 2
void LoginContentWidget::setip(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 2, _a);
}

// SIGNAL 3
void LoginContentWidget::sigNoExam(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 3, _a);
}
struct qt_meta_stringdata_LoginWidget_t {
    QByteArrayData data[9];
    char stringdata0[83];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_LoginWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_LoginWidget_t qt_meta_stringdata_LoginWidget = {
    {
QT_MOC_LITERAL(0, 0, 11), // "LoginWidget"
QT_MOC_LITERAL(1, 12, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 32, 0), // ""
QT_MOC_LITERAL(3, 33, 8), // "curIndex"
QT_MOC_LITERAL(4, 42, 15), // "setExamViewData"
QT_MOC_LITERAL(5, 58, 3), // "ids"
QT_MOC_LITERAL(6, 62, 5), // "names"
QT_MOC_LITERAL(7, 68, 9), // "sigNoExam"
QT_MOC_LITERAL(8, 78, 4) // "name"

    },
    "LoginWidget\0turnMainContentPage\0\0"
    "curIndex\0setExamViewData\0ids\0names\0"
    "sigNoExam\0name"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_LoginWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       3,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       3,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   29,    2, 0x06 /* Public */,
       4,    2,   32,    2, 0x06 /* Public */,
       7,    1,   37,    2, 0x06 /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,
    QMetaType::Void, QMetaType::QStringList, QMetaType::QStringList,    5,    6,
    QMetaType::Void, QMetaType::QString,    8,

       0        // eod
};

void LoginWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        LoginWidget *_t = static_cast<LoginWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 1: _t->setExamViewData((*reinterpret_cast< QStringList(*)>(_a[1])),(*reinterpret_cast< QStringList(*)>(_a[2]))); break;
        case 2: _t->sigNoExam((*reinterpret_cast< QString(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (LoginWidget::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&LoginWidget::turnMainContentPage)) {
                *result = 0;
            }
        }
        {
            typedef void (LoginWidget::*_t)(QStringList , QStringList );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&LoginWidget::setExamViewData)) {
                *result = 1;
            }
        }
        {
            typedef void (LoginWidget::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&LoginWidget::sigNoExam)) {
                *result = 2;
            }
        }
    }
}

const QMetaObject LoginWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_LoginWidget.data,
      qt_meta_data_LoginWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *LoginWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *LoginWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_LoginWidget.stringdata0))
        return static_cast<void*>(const_cast< LoginWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int LoginWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
void LoginWidget::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void LoginWidget::setExamViewData(QStringList _t1, QStringList _t2)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)), const_cast<void*>(reinterpret_cast<const void*>(&_t2)) };
    QMetaObject::activate(this, &staticMetaObject, 1, _a);
}

// SIGNAL 2
void LoginWidget::sigNoExam(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 2, _a);
}
QT_END_MOC_NAMESPACE
