/****************************************************************************
** Meta object code from reading C++ file 'selectexamwidget.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/selectexam/selectexamwidget.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'selectexamwidget.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_SelectExamContentWidget_t {
    QByteArrayData data[4];
    char stringdata0[54];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_SelectExamContentWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_SelectExamContentWidget_t qt_meta_stringdata_SelectExamContentWidget = {
    {
QT_MOC_LITERAL(0, 0, 23), // "SelectExamContentWidget"
QT_MOC_LITERAL(1, 24, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 44, 0), // ""
QT_MOC_LITERAL(3, 45, 8) // "curIndex"

    },
    "SelectExamContentWidget\0turnMainContentPage\0"
    "\0curIndex"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_SelectExamContentWidget[] = {

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

void SelectExamContentWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        SelectExamContentWidget *_t = static_cast<SelectExamContentWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (SelectExamContentWidget::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&SelectExamContentWidget::turnMainContentPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject SelectExamContentWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_SelectExamContentWidget.data,
      qt_meta_data_SelectExamContentWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *SelectExamContentWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *SelectExamContentWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_SelectExamContentWidget.stringdata0))
        return static_cast<void*>(const_cast< SelectExamContentWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int SelectExamContentWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
void SelectExamContentWidget::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
struct qt_meta_stringdata_SelectExamWidget_t {
    QByteArrayData data[7];
    char stringdata0[73];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_SelectExamWidget_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_SelectExamWidget_t qt_meta_stringdata_SelectExamWidget = {
    {
QT_MOC_LITERAL(0, 0, 16), // "SelectExamWidget"
QT_MOC_LITERAL(1, 17, 19), // "turnMainContentPage"
QT_MOC_LITERAL(2, 37, 0), // ""
QT_MOC_LITERAL(3, 38, 8), // "curIndex"
QT_MOC_LITERAL(4, 47, 15), // "setExamViewData"
QT_MOC_LITERAL(5, 63, 3), // "ids"
QT_MOC_LITERAL(6, 67, 5) // "names"

    },
    "SelectExamWidget\0turnMainContentPage\0"
    "\0curIndex\0setExamViewData\0ids\0names"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_SelectExamWidget[] = {

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
       4,    2,   27,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::Int,    3,

 // slots: parameters
    QMetaType::Void, QMetaType::QStringList, QMetaType::QStringList,    5,    6,

       0        // eod
};

void SelectExamWidget::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        SelectExamWidget *_t = static_cast<SelectExamWidget *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->turnMainContentPage((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 1: _t->setExamViewData((*reinterpret_cast< QStringList(*)>(_a[1])),(*reinterpret_cast< QStringList(*)>(_a[2]))); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (SelectExamWidget::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&SelectExamWidget::turnMainContentPage)) {
                *result = 0;
            }
        }
    }
}

const QMetaObject SelectExamWidget::staticMetaObject = {
    { &QWidget::staticMetaObject, qt_meta_stringdata_SelectExamWidget.data,
      qt_meta_data_SelectExamWidget,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *SelectExamWidget::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *SelectExamWidget::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_SelectExamWidget.stringdata0))
        return static_cast<void*>(const_cast< SelectExamWidget*>(this));
    return QWidget::qt_metacast(_clname);
}

int SelectExamWidget::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
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
void SelectExamWidget::turnMainContentPage(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}
QT_END_MOC_NAMESPACE
