/****************************************************************************
** Meta object code from reading C++ file 'tip.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../public/tip.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'tip.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_BindNoTip_t {
    QByteArrayData data[1];
    char stringdata0[10];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_BindNoTip_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_BindNoTip_t qt_meta_stringdata_BindNoTip = {
    {
QT_MOC_LITERAL(0, 0, 9) // "BindNoTip"

    },
    "BindNoTip"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_BindNoTip[] = {

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

void BindNoTip::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    Q_UNUSED(_o);
    Q_UNUSED(_id);
    Q_UNUSED(_c);
    Q_UNUSED(_a);
}

const QMetaObject BindNoTip::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_BindNoTip.data,
      qt_meta_data_BindNoTip,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *BindNoTip::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *BindNoTip::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_BindNoTip.stringdata0))
        return static_cast<void*>(const_cast< BindNoTip*>(this));
    return QWidget::qt_metacast(_clname);
}

int BindNoTip::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    return _id;
}
struct qt_meta_stringdata_BindTip_t {
    QByteArrayData data[12];
    char stringdata0[109];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_BindTip_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_BindTip_t qt_meta_stringdata_BindTip = {
    {
QT_MOC_LITERAL(0, 0, 7), // "BindTip"
QT_MOC_LITERAL(1, 8, 8), // "turnPage"
QT_MOC_LITERAL(2, 17, 0), // ""
QT_MOC_LITERAL(3, 18, 8), // "curIndex"
QT_MOC_LITERAL(4, 27, 7), // "setPage"
QT_MOC_LITERAL(5, 35, 6), // "setTip"
QT_MOC_LITERAL(6, 42, 3), // "tip"
QT_MOC_LITERAL(7, 46, 11), // "setWatchTip"
QT_MOC_LITERAL(8, 58, 10), // "setBindTip"
QT_MOC_LITERAL(9, 69, 8), // "clearTip"
QT_MOC_LITERAL(10, 78, 15), // "StartClearClock"
QT_MOC_LITERAL(11, 94, 14) // "StopClearClock"

    },
    "BindTip\0turnPage\0\0curIndex\0setPage\0"
    "setTip\0tip\0setWatchTip\0setBindTip\0"
    "clearTip\0StartClearClock\0StopClearClock"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_BindTip[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       8,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       1,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   54,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       4,    0,   57,    2, 0x0a /* Public */,
       5,    1,   58,    2, 0x0a /* Public */,
       7,    1,   61,    2, 0x0a /* Public */,
       8,    1,   64,    2, 0x0a /* Public */,
       9,    0,   67,    2, 0x0a /* Public */,
      10,    0,   68,    2, 0x0a /* Public */,
      11,    0,   69,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,

 // slots: parameters
    QMetaType::Void,
    QMetaType::Void, QMetaType::QString,    6,
    QMetaType::Void, QMetaType::QString,    6,
    QMetaType::Void, QMetaType::QString,    6,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,

       0        // eod
};

void BindTip::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        BindTip *_t = static_cast<BindTip *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 1: _t->setPage(); break;
        case 2: _t->setTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 3: _t->setWatchTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 4: _t->setBindTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 5: _t->clearTip(); break;
        case 6: _t->StartClearClock(); break;
        case 7: _t->StopClearClock(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (BindTip::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&BindTip::turnPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject BindTip::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_BindTip.data,
      qt_meta_data_BindTip,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *BindTip::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *BindTip::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_BindTip.stringdata0))
        return static_cast<void*>(const_cast< BindTip*>(this));
    return QWidget::qt_metacast(_clname);
}

int BindTip::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QWidget::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 8)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 8;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 8)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 8;
    }
    return _id;
}

// SIGNAL 0
void BindTip::turnPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
QT_END_MOC_NAMESPACE
