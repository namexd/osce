/****************************************************************************
** Meta object code from reading C++ file 'mainhead.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/mainhead.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'mainhead.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_MainHead_t {
    QByteArrayData data[5];
    char stringdata0[38];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_MainHead_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_MainHead_t qt_meta_stringdata_MainHead = {
    {
QT_MOC_LITERAL(0, 0, 8), // "MainHead"
QT_MOC_LITERAL(1, 9, 7), // "showMin"
QT_MOC_LITERAL(2, 17, 0), // ""
QT_MOC_LITERAL(3, 18, 7), // "showMax"
QT_MOC_LITERAL(4, 26, 11) // "closeWidget"

    },
    "MainHead\0showMin\0\0showMax\0closeWidget"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_MainHead[] = {

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
       1,    0,   29,    2, 0x06 /* Public */,
       3,    0,   30,    2, 0x06 /* Public */,
       4,    0,   31,    2, 0x06 /* Public */,

 // signals: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,

       0        // eod
};

void MainHead::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        MainHead *_t = static_cast<MainHead *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->showMin(); break;
        case 1: _t->showMax(); break;
        case 2: _t->closeWidget(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (MainHead::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&MainHead::showMin)) {
                *result = 0;
            }
        }
        {
            typedef void (MainHead::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&MainHead::showMax)) {
                *result = 1;
            }
        }
        {
            typedef void (MainHead::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&MainHead::closeWidget)) {
                *result = 2;
            }
        }
    }
    Q_UNUSED(_a);
}

const QMetaObject MainHead::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_MainHead.data,
      qt_meta_data_MainHead,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *MainHead::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *MainHead::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_MainHead.stringdata0))
        return static_cast<void*>(const_cast< MainHead*>(this));
    return QWidget::qt_metacast(_clname);
}

int MainHead::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
void MainHead::showMin()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}

// SIGNAL 1
void MainHead::showMax()
{
    QMetaObject::activate(this, &staticMetaObject, 1, Q_NULLPTR);
}

// SIGNAL 2
void MainHead::closeWidget()
{
    QMetaObject::activate(this, &staticMetaObject, 2, Q_NULLPTR);
}
QT_END_MOC_NAMESPACE
