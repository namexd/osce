/****************************************************************************
** Meta object code from reading C++ file 'activitylabel.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../public/activitylabel.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'activitylabel.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_ActivityLabel_t {
    QByteArrayData data[13];
    char stringdata0[105];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ActivityLabel_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ActivityLabel_t qt_meta_stringdata_ActivityLabel = {
    {
QT_MOC_LITERAL(0, 0, 13), // "ActivityLabel"
QT_MOC_LITERAL(1, 14, 7), // "clicked"
QT_MOC_LITERAL(2, 22, 0), // ""
QT_MOC_LITERAL(3, 23, 5), // "hover"
QT_MOC_LITERAL(4, 29, 5), // "press"
QT_MOC_LITERAL(5, 35, 5), // "leave"
QT_MOC_LITERAL(6, 41, 4), // "move"
QT_MOC_LITERAL(7, 46, 6), // "Lhover"
QT_MOC_LITERAL(8, 53, 8), // "Lclicked"
QT_MOC_LITERAL(9, 62, 6), // "Lleave"
QT_MOC_LITERAL(10, 69, 11), // "changeHover"
QT_MOC_LITERAL(11, 81, 11), // "changeClick"
QT_MOC_LITERAL(12, 93, 11) // "changeLeave"

    },
    "ActivityLabel\0clicked\0\0hover\0press\0"
    "leave\0move\0Lhover\0Lclicked\0Lleave\0"
    "changeHover\0changeClick\0changeLeave"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ActivityLabel[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
      11,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       8,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    0,   69,    2, 0x06 /* Public */,
       3,    0,   70,    2, 0x06 /* Public */,
       4,    0,   71,    2, 0x06 /* Public */,
       5,    0,   72,    2, 0x06 /* Public */,
       6,    0,   73,    2, 0x06 /* Public */,
       7,    0,   74,    2, 0x06 /* Public */,
       8,    0,   75,    2, 0x06 /* Public */,
       9,    0,   76,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
      10,    0,   77,    2, 0x0a /* Public */,
      11,    0,   78,    2, 0x0a /* Public */,
      12,    0,   79,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,

 // slots: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,

       0        // eod
};

void ActivityLabel::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ActivityLabel *_t = static_cast<ActivityLabel *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->clicked(); break;
        case 1: _t->hover(); break;
        case 2: _t->press(); break;
        case 3: _t->leave(); break;
        case 4: _t->move(); break;
        case 5: _t->Lhover(); break;
        case 6: _t->Lclicked(); break;
        case 7: _t->Lleave(); break;
        case 8: _t->changeHover(); break;
        case 9: _t->changeClick(); break;
        case 10: _t->changeLeave(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::clicked)) {
                *result = 0;
            }
        }
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::hover)) {
                *result = 1;
            }
        }
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::press)) {
                *result = 2;
            }
        }
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::leave)) {
                *result = 3;
            }
        }
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::move)) {
                *result = 4;
            }
        }
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::Lhover)) {
                *result = 5;
            }
        }
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::Lclicked)) {
                *result = 6;
            }
        }
        {
            typedef void (ActivityLabel::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ActivityLabel::Lleave)) {
                *result = 7;
            }
        }
    }
    Q_UNUSED(_a);
}

const QMetaObject ActivityLabel::staticMetaObject = {
    { &QLabel::staticMetaObject, qt_meta_stringdata_ActivityLabel.data,
      qt_meta_data_ActivityLabel,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ActivityLabel::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ActivityLabel::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ActivityLabel.stringdata0))
        return static_cast<void*>(const_cast< ActivityLabel*>(this));
    return QLabel::qt_metacast(_clname);
}

int ActivityLabel::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QLabel::qt_metacall(_c, _id, _a);
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
void ActivityLabel::clicked()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}

// SIGNAL 1
void ActivityLabel::hover()
{
    QMetaObject::activate(this, &staticMetaObject, 1, Q_NULLPTR);
}

// SIGNAL 2
void ActivityLabel::press()
{
    QMetaObject::activate(this, &staticMetaObject, 2, Q_NULLPTR);
}

// SIGNAL 3
void ActivityLabel::leave()
{
    QMetaObject::activate(this, &staticMetaObject, 3, Q_NULLPTR);
}

// SIGNAL 4
void ActivityLabel::move()
{
    QMetaObject::activate(this, &staticMetaObject, 4, Q_NULLPTR);
}

// SIGNAL 5
void ActivityLabel::Lhover()
{
    QMetaObject::activate(this, &staticMetaObject, 5, Q_NULLPTR);
}

// SIGNAL 6
void ActivityLabel::Lclicked()
{
    QMetaObject::activate(this, &staticMetaObject, 6, Q_NULLPTR);
}

// SIGNAL 7
void ActivityLabel::Lleave()
{
    QMetaObject::activate(this, &staticMetaObject, 7, Q_NULLPTR);
}
QT_END_MOC_NAMESPACE
