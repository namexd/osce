/****************************************************************************
** Meta object code from reading C++ file 'calendar.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../public/calendar.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'calendar.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_CalendarItem_t {
    QByteArrayData data[13];
    char stringdata0[108];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_CalendarItem_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_CalendarItem_t qt_meta_stringdata_CalendarItem = {
    {
QT_MOC_LITERAL(0, 0, 12), // "CalendarItem"
QT_MOC_LITERAL(1, 13, 7), // "retDate"
QT_MOC_LITERAL(2, 21, 0), // ""
QT_MOC_LITERAL(3, 22, 4), // "date"
QT_MOC_LITERAL(4, 27, 11), // "closeWidget"
QT_MOC_LITERAL(5, 39, 7), // "addYear"
QT_MOC_LITERAL(6, 47, 7), // "subYear"
QT_MOC_LITERAL(7, 55, 8), // "addMonth"
QT_MOC_LITERAL(8, 64, 8), // "subMonth"
QT_MOC_LITERAL(9, 73, 6), // "addDay"
QT_MOC_LITERAL(10, 80, 6), // "subDay"
QT_MOC_LITERAL(11, 87, 6), // "setRet"
QT_MOC_LITERAL(12, 94, 13) // "setTodayTitle"

    },
    "CalendarItem\0retDate\0\0date\0closeWidget\0"
    "addYear\0subYear\0addMonth\0subMonth\0"
    "addDay\0subDay\0setRet\0setTodayTitle"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_CalendarItem[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
      10,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       2,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   64,    2, 0x06 /* Public */,
       4,    0,   67,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
       5,    0,   68,    2, 0x0a /* Public */,
       6,    0,   69,    2, 0x0a /* Public */,
       7,    0,   70,    2, 0x0a /* Public */,
       8,    0,   71,    2, 0x0a /* Public */,
       9,    0,   72,    2, 0x0a /* Public */,
      10,    0,   73,    2, 0x0a /* Public */,
      11,    0,   74,    2, 0x0a /* Public */,
      12,    0,   75,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void,

 // slots: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,

       0        // eod
};

void CalendarItem::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        CalendarItem *_t = static_cast<CalendarItem *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->retDate((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 1: _t->closeWidget(); break;
        case 2: _t->addYear(); break;
        case 3: _t->subYear(); break;
        case 4: _t->addMonth(); break;
        case 5: _t->subMonth(); break;
        case 6: _t->addDay(); break;
        case 7: _t->subDay(); break;
        case 8: _t->setRet(); break;
        case 9: _t->setTodayTitle(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (CalendarItem::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&CalendarItem::retDate)) {
                *result = 0;
            }
        }
        {
            typedef void (CalendarItem::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&CalendarItem::closeWidget)) {
                *result = 1;
            }
        }
    }
}

const QMetaObject CalendarItem::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_CalendarItem.data,
      qt_meta_data_CalendarItem,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *CalendarItem::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *CalendarItem::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_CalendarItem.stringdata0))
        return static_cast<void*>(const_cast< CalendarItem*>(this));
    return QWidget::qt_metacast(_clname);
}

int CalendarItem::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
void CalendarItem::retDate(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void CalendarItem::closeWidget()
{
    QMetaObject::activate(this, &staticMetaObject, 1, Q_NULLPTR);
}
struct qt_meta_stringdata_CalendarMenu_t {
    QByteArrayData data[5];
    char stringdata0[39];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_CalendarMenu_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_CalendarMenu_t qt_meta_stringdata_CalendarMenu = {
    {
QT_MOC_LITERAL(0, 0, 12), // "CalendarMenu"
QT_MOC_LITERAL(1, 13, 7), // "retDate"
QT_MOC_LITERAL(2, 21, 0), // ""
QT_MOC_LITERAL(3, 22, 4), // "date"
QT_MOC_LITERAL(4, 27, 11) // "closeWidget"

    },
    "CalendarMenu\0retDate\0\0date\0closeWidget"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_CalendarMenu[] = {

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
       4,    0,   27,    2, 0x06 /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void,

       0        // eod
};

void CalendarMenu::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        CalendarMenu *_t = static_cast<CalendarMenu *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->retDate((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 1: _t->closeWidget(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (CalendarMenu::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&CalendarMenu::retDate)) {
                *result = 0;
            }
        }
        {
            typedef void (CalendarMenu::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&CalendarMenu::closeWidget)) {
                *result = 1;
            }
        }
    }
}

const QMetaObject CalendarMenu::staticMetaObject = {
    { &QMenu::staticMetaObject, qt_meta_stringdata_CalendarMenu.data,
      qt_meta_data_CalendarMenu,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *CalendarMenu::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *CalendarMenu::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_CalendarMenu.stringdata0))
        return static_cast<void*>(const_cast< CalendarMenu*>(this));
    return QMenu::qt_metacast(_clname);
}

int CalendarMenu::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QMenu::qt_metacall(_c, _id, _a);
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
void CalendarMenu::retDate(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void CalendarMenu::closeWidget()
{
    QMetaObject::activate(this, &staticMetaObject, 1, Q_NULLPTR);
}
struct qt_meta_stringdata_ClickEdit_t {
    QByteArrayData data[5];
    char stringdata0[35];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ClickEdit_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ClickEdit_t qt_meta_stringdata_ClickEdit = {
    {
QT_MOC_LITERAL(0, 0, 9), // "ClickEdit"
QT_MOC_LITERAL(1, 10, 7), // "clicked"
QT_MOC_LITERAL(2, 18, 0), // ""
QT_MOC_LITERAL(3, 19, 8), // "QWidget*"
QT_MOC_LITERAL(4, 28, 6) // "widget"

    },
    "ClickEdit\0clicked\0\0QWidget*\0widget"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ClickEdit[] = {

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
    QMetaType::Void, 0x80000000 | 3,    4,

       0        // eod
};

void ClickEdit::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ClickEdit *_t = static_cast<ClickEdit *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->clicked((*reinterpret_cast< QWidget*(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        switch (_id) {
        default: *reinterpret_cast<int*>(_a[0]) = -1; break;
        case 0:
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
            typedef void (ClickEdit::*_t)(QWidget * );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ClickEdit::clicked)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject ClickEdit::staticMetaObject = {
    { &QLineEdit::staticMetaObject, qt_meta_stringdata_ClickEdit.data,
      qt_meta_data_ClickEdit,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ClickEdit::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ClickEdit::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ClickEdit.stringdata0))
        return static_cast<void*>(const_cast< ClickEdit*>(this));
    return QLineEdit::qt_metacast(_clname);
}

int ClickEdit::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QLineEdit::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 1)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 1;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 1)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 1;
    }
    return _id;
}

// SIGNAL 0
void ClickEdit::clicked(QWidget * _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
QT_END_MOC_NAMESPACE
