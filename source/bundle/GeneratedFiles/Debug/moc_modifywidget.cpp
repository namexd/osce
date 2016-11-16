/****************************************************************************
** Meta object code from reading C++ file 'modifywidget.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/watchmanager/modifywidget.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'modifywidget.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_ModifyHeadWidget_t {
    QByteArrayData data[4];
    char stringdata0[39];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ModifyHeadWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ModifyHeadWidget_t qt_meta_stringdata_ModifyHeadWidget = {
    {
QT_MOC_LITERAL(0, 0, 16), // "ModifyHeadWidget"
QT_MOC_LITERAL(1, 17, 11), // "CloseWidget"
QT_MOC_LITERAL(2, 29, 0), // ""
QT_MOC_LITERAL(3, 30, 8) // "sigclose"

    },
    "ModifyHeadWidget\0CloseWidget\0\0sigclose"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ModifyHeadWidget[] = {

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
       1,    0,   24,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       3,    0,   25,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void,

 // slots: parameters
    QMetaType::Void,

       0        // eod
};

void ModifyHeadWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ModifyHeadWidget *_t = static_cast<ModifyHeadWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->CloseWidget(); break;
        case 1: _t->sigclose(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ModifyHeadWidget::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ModifyHeadWidget::CloseWidget)) {
                *result = 0;
            }
        }
    }
    Q_UNUSED(_a);
}

const QMetaObject ModifyHeadWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_ModifyHeadWidget.data,
      qt_meta_data_ModifyHeadWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ModifyHeadWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ModifyHeadWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ModifyHeadWidget.stringdata0))
        return static_cast<void*>(const_cast< ModifyHeadWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int ModifyHeadWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
void ModifyHeadWidget::CloseWidget()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}
struct qt_meta_stringdata_ModifyContentWidget_t {
    QByteArrayData data[16];
    char stringdata0[160];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ModifyContentWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ModifyContentWidget_t qt_meta_stringdata_ModifyContentWidget = {
    {
QT_MOC_LITERAL(0, 0, 19), // "ModifyContentWidget"
QT_MOC_LITERAL(1, 20, 8), // "closePop"
QT_MOC_LITERAL(2, 29, 0), // ""
QT_MOC_LITERAL(3, 30, 11), // "CloseWidget"
QT_MOC_LITERAL(4, 42, 12), // "calendarSlot"
QT_MOC_LITERAL(5, 55, 8), // "QWidget*"
QT_MOC_LITERAL(6, 64, 6), // "widget"
QT_MOC_LITERAL(7, 71, 19), // "setPurchaseTimeEdit"
QT_MOC_LITERAL(8, 91, 4), // "date"
QT_MOC_LITERAL(9, 96, 8), // "setIndex"
QT_MOC_LITERAL(10, 105, 4), // "data"
QT_MOC_LITERAL(11, 110, 10), // "SubmitData"
QT_MOC_LITERAL(12, 121, 14), // "recevieRequest"
QT_MOC_LITERAL(13, 136, 4), // "stat"
QT_MOC_LITERAL(14, 141, 4), // "type"
QT_MOC_LITERAL(15, 146, 13) // "recevieByCode"

    },
    "ModifyContentWidget\0closePop\0\0CloseWidget\0"
    "calendarSlot\0QWidget*\0widget\0"
    "setPurchaseTimeEdit\0date\0setIndex\0"
    "data\0SubmitData\0recevieRequest\0stat\0"
    "type\0recevieByCode"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ModifyContentWidget[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       8,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       2,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    0,   54,    2, 0x06 /* Public */,
       3,    0,   55,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       4,    1,   56,    2, 0x0a /* Public */,
       7,    1,   59,    2, 0x0a /* Public */,
       9,    1,   62,    2, 0x0a /* Public */,
      11,    0,   65,    2, 0x0a /* Public */,
      12,    3,   66,    2, 0x0a /* Public */,
      15,    2,   73,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void,
    QMetaType::Void,

 // slots: parameters
    QMetaType::Void, 0x80000000 | 5,    6,
    QMetaType::Void, QMetaType::QString,    8,
    QMetaType::Void, QMetaType::Int,   10,
    QMetaType::Void,
    QMetaType::Void, QMetaType::Int, QMetaType::QString, QMetaType::Int,   13,   10,   14,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,   13,   10,

       0        // eod
};

void ModifyContentWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ModifyContentWidget *_t = static_cast<ModifyContentWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->closePop(); break;
        case 1: _t->CloseWidget(); break;
        case 2: _t->calendarSlot((*reinterpret_cast< QWidget*(*)>(_a[1]))); break;
        case 3: _t->setPurchaseTimeEdit((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 4: _t->setIndex((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 5: _t->SubmitData(); break;
        case 6: _t->recevieRequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2])),(*reinterpret_cast< int(*)>(_a[3]))); break;
        case 7: _t->recevieByCode((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        switch (_id) {
        default: *reinterpret_cast<int*>(_a[0]) = -1; break;
        case 2:
            switch (*reinterpret_cast<int*>(_a[1])) {
            default: *reinterpret_cast<int*>(_a[0]) = -1; break;
            case 0:
                *reinterpret_cast<int*>(_a[0]) = qRegisterMetaType< QWidget* >(); break;
            }
            break;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ModifyContentWidget::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ModifyContentWidget::closePop)) {
                *result = 0;
            }
        }
        {
            typedef void (ModifyContentWidget::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ModifyContentWidget::CloseWidget)) {
                *result = 1;
            }
        }
    }
}

const QMetaObject ModifyContentWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_ModifyContentWidget.data,
      qt_meta_data_ModifyContentWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ModifyContentWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ModifyContentWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ModifyContentWidget.stringdata0))
        return static_cast<void*>(const_cast< ModifyContentWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int ModifyContentWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
            qt_static_metacall(this, _c, _id, _a);
        _id -= 8;
    }
    return _id;
}

// SIGNAL 0
void ModifyContentWidget::closePop()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}

// SIGNAL 1
void ModifyContentWidget::CloseWidget()
{
    QMetaObject::activate(this, &staticMetaObject, 1, Q_NULLPTR);
}
struct qt_meta_stringdata_ModifyWidget_t {
    QByteArrayData data[3];
    char stringdata0[26];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ModifyWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ModifyWidget_t qt_meta_stringdata_ModifyWidget = {
    {
QT_MOC_LITERAL(0, 0, 12), // "ModifyWidget"
QT_MOC_LITERAL(1, 13, 11), // "CloseWidget"
QT_MOC_LITERAL(2, 25, 0) // ""

    },
    "ModifyWidget\0CloseWidget\0"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ModifyWidget[] = {

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
       1,    0,   19,    2, 0x06 /* Public */,

 // signals: parameters
    QMetaType::Void,

       0        // eod
};

void ModifyWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ModifyWidget *_t = static_cast<ModifyWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->CloseWidget(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ModifyWidget::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ModifyWidget::CloseWidget)) {
                *result = 0;
            }
        }
    }
    Q_UNUSED(_a);
}

const QMetaObject ModifyWidget::staticMetaObject = {
    { &DropShadowWidget::staticMetaObject, qt_meta_stringdata_ModifyWidget.data,
      qt_meta_data_ModifyWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ModifyWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ModifyWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ModifyWidget.stringdata0))
        return static_cast<void*>(const_cast< ModifyWidget*>(this));
    return DropShadowWidget::qt_metacast(_clname);
}

int ModifyWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = DropShadowWidget::qt_metacall(_c, _id, _a);
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
void ModifyWidget::CloseWidget()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}
QT_END_MOC_NAMESPACE
