/****************************************************************************
** Meta object code from reading C++ file 'readcards.h'
**
** Created by: The Qt Meta Object Compiler version 67 (Qt 5.5.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "../../maininterface/bind/readcards.h"
#include <QtCore/qbytearray.h>
#include <QtCore/qmetatype.h>
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'readcards.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 67
#error "This file was generated using the moc from 5.5.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
struct qt_meta_stringdata_ReadIDCard_t {
    QByteArrayData data[21];
    char stringdata0[249];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ReadIDCard_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ReadIDCard_t qt_meta_stringdata_ReadIDCard = {
    {
QT_MOC_LITERAL(0, 0, 10), // "ReadIDCard"
QT_MOC_LITERAL(1, 11, 14), // "FindDeviceFail"
QT_MOC_LITERAL(2, 26, 0), // ""
QT_MOC_LITERAL(3, 27, 18), // "OpenIDCardPortFail"
QT_MOC_LITERAL(4, 46, 14), // "ReadIDcardFail"
QT_MOC_LITERAL(5, 61, 16), // "SelectIDcardFail"
QT_MOC_LITERAL(6, 78, 14), // "FindIDcardFail"
QT_MOC_LITERAL(7, 93, 11), // "CanReadCard"
QT_MOC_LITERAL(8, 105, 13), // "cacheIDCardNO"
QT_MOC_LITERAL(9, 119, 4), // "data"
QT_MOC_LITERAL(10, 124, 4), // "name"
QT_MOC_LITERAL(11, 129, 8), // "cacheTip"
QT_MOC_LITERAL(12, 138, 3), // "tip"
QT_MOC_LITERAL(13, 142, 13), // "connectDevice"
QT_MOC_LITERAL(14, 156, 4), // "stat"
QT_MOC_LITERAL(15, 161, 18), // "IDCardClockTimeout"
QT_MOC_LITERAL(16, 180, 20), // "FindReadIDCardDevice"
QT_MOC_LITERAL(17, 201, 12), // "SelectIDCard"
QT_MOC_LITERAL(18, 214, 10), // "ReadCardNO"
QT_MOC_LITERAL(19, 225, 10), // "FindIDCard"
QT_MOC_LITERAL(20, 236, 12) // "ReadIDCardNO"

    },
    "ReadIDCard\0FindDeviceFail\0\0"
    "OpenIDCardPortFail\0ReadIDcardFail\0"
    "SelectIDcardFail\0FindIDcardFail\0"
    "CanReadCard\0cacheIDCardNO\0data\0name\0"
    "cacheTip\0tip\0connectDevice\0stat\0"
    "IDCardClockTimeout\0FindReadIDCardDevice\0"
    "SelectIDCard\0ReadCardNO\0FindIDCard\0"
    "ReadIDCardNO"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ReadIDCard[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
      15,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       9,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    0,   89,    2, 0x06 /* Public */,
       3,    0,   90,    2, 0x06 /* Public */,
       4,    0,   91,    2, 0x06 /* Public */,
       5,    0,   92,    2, 0x06 /* Public */,
       6,    0,   93,    2, 0x06 /* Public */,
       7,    0,   94,    2, 0x06 /* Public */,
       8,    2,   95,    2, 0x06 /* Public */,
      11,    1,  100,    2, 0x06 /* Public */,
      13,    1,  103,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
      15,    0,  106,    2, 0x0a /* Public */,
      16,    0,  107,    2, 0x0a /* Public */,
      17,    0,  108,    2, 0x0a /* Public */,
      18,    0,  109,    2, 0x0a /* Public */,
      19,    0,  110,    2, 0x0a /* Public */,
      20,    0,  111,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void, QMetaType::QString, QMetaType::QString,    9,   10,
    QMetaType::Void, QMetaType::QString,   12,
    QMetaType::Void, QMetaType::Int,   14,

 // slots: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Bool,
    QMetaType::Bool,
    QMetaType::Bool,
    QMetaType::Void,

       0        // eod
};

void ReadIDCard::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ReadIDCard *_t = static_cast<ReadIDCard *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->FindDeviceFail(); break;
        case 1: _t->OpenIDCardPortFail(); break;
        case 2: _t->ReadIDcardFail(); break;
        case 3: _t->SelectIDcardFail(); break;
        case 4: _t->FindIDcardFail(); break;
        case 5: _t->CanReadCard(); break;
        case 6: _t->cacheIDCardNO((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 7: _t->cacheTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 8: _t->connectDevice((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 9: _t->IDCardClockTimeout(); break;
        case 10: _t->FindReadIDCardDevice(); break;
        case 11: { bool _r = _t->SelectIDCard();
            if (_a[0]) *reinterpret_cast< bool*>(_a[0]) = _r; }  break;
        case 12: { bool _r = _t->ReadCardNO();
            if (_a[0]) *reinterpret_cast< bool*>(_a[0]) = _r; }  break;
        case 13: { bool _r = _t->FindIDCard();
            if (_a[0]) *reinterpret_cast< bool*>(_a[0]) = _r; }  break;
        case 14: _t->ReadIDCardNO(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ReadIDCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::FindDeviceFail)) {
                *result = 0;
            }
        }
        {
            typedef void (ReadIDCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::OpenIDCardPortFail)) {
                *result = 1;
            }
        }
        {
            typedef void (ReadIDCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::ReadIDcardFail)) {
                *result = 2;
            }
        }
        {
            typedef void (ReadIDCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::SelectIDcardFail)) {
                *result = 3;
            }
        }
        {
            typedef void (ReadIDCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::FindIDcardFail)) {
                *result = 4;
            }
        }
        {
            typedef void (ReadIDCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::CanReadCard)) {
                *result = 5;
            }
        }
        {
            typedef void (ReadIDCard::*_t)(QString , QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::cacheIDCardNO)) {
                *result = 6;
            }
        }
        {
            typedef void (ReadIDCard::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::cacheTip)) {
                *result = 7;
            }
        }
        {
            typedef void (ReadIDCard::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadIDCard::connectDevice)) {
                *result = 8;
            }
        }
    }
}

const QMetaObject ReadIDCard::staticMetaObject = {
    { &QObject::staticMetaObject, qt_meta_stringdata_ReadIDCard.data,
      qt_meta_data_ReadIDCard,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ReadIDCard::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ReadIDCard::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ReadIDCard.stringdata0))
        return static_cast<void*>(const_cast< ReadIDCard*>(this));
    return QObject::qt_metacast(_clname);
}

int ReadIDCard::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QObject::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 15)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 15;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 15)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 15;
    }
    return _id;
}

// SIGNAL 0
void ReadIDCard::FindDeviceFail()
{
    QMetaObject::activate(this, &staticMetaObject, 0, Q_NULLPTR);
}

// SIGNAL 1
void ReadIDCard::OpenIDCardPortFail()
{
    QMetaObject::activate(this, &staticMetaObject, 1, Q_NULLPTR);
}

// SIGNAL 2
void ReadIDCard::ReadIDcardFail()
{
    QMetaObject::activate(this, &staticMetaObject, 2, Q_NULLPTR);
}

// SIGNAL 3
void ReadIDCard::SelectIDcardFail()
{
    QMetaObject::activate(this, &staticMetaObject, 3, Q_NULLPTR);
}

// SIGNAL 4
void ReadIDCard::FindIDcardFail()
{
    QMetaObject::activate(this, &staticMetaObject, 4, Q_NULLPTR);
}

// SIGNAL 5
void ReadIDCard::CanReadCard()
{
    QMetaObject::activate(this, &staticMetaObject, 5, Q_NULLPTR);
}

// SIGNAL 6
void ReadIDCard::cacheIDCardNO(QString _t1, QString _t2)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)), const_cast<void*>(reinterpret_cast<const void*>(&_t2)) };
    QMetaObject::activate(this, &staticMetaObject, 6, _a);
}

// SIGNAL 7
void ReadIDCard::cacheTip(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 7, _a);
}

// SIGNAL 8
void ReadIDCard::connectDevice(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 8, _a);
}
struct qt_meta_stringdata_ReadSmartCard_t {
    QByteArrayData data[13];
    char stringdata0[133];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ReadSmartCard_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ReadSmartCard_t qt_meta_stringdata_ReadSmartCard = {
    {
QT_MOC_LITERAL(0, 0, 13), // "ReadSmartCard"
QT_MOC_LITERAL(1, 14, 12), // "cacheSmartID"
QT_MOC_LITERAL(2, 27, 0), // ""
QT_MOC_LITERAL(3, 28, 2), // "id"
QT_MOC_LITERAL(4, 31, 14), // "siginitLoadDll"
QT_MOC_LITERAL(5, 46, 8), // "initPort"
QT_MOC_LITERAL(6, 55, 8), // "cacheTip"
QT_MOC_LITERAL(7, 64, 3), // "tip"
QT_MOC_LITERAL(8, 68, 13), // "connectDevice"
QT_MOC_LITERAL(9, 82, 4), // "stat"
QT_MOC_LITERAL(10, 87, 11), // "initLoadLib"
QT_MOC_LITERAL(11, 99, 17), // "FindEhuoyanDevice"
QT_MOC_LITERAL(12, 117, 15) // "SearchSmartCard"

    },
    "ReadSmartCard\0cacheSmartID\0\0id\0"
    "siginitLoadDll\0initPort\0cacheTip\0tip\0"
    "connectDevice\0stat\0initLoadLib\0"
    "FindEhuoyanDevice\0SearchSmartCard"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ReadSmartCard[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
       8,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       5,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,   54,    2, 0x06 /* Public */,
       4,    0,   57,    2, 0x06 /* Public */,
       5,    0,   58,    2, 0x06 /* Public */,
       6,    1,   59,    2, 0x06 /* Public */,
       8,    1,   62,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
      10,    0,   65,    2, 0x0a /* Public */,
      11,    0,   66,    2, 0x0a /* Public */,
      12,    0,   67,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void, QMetaType::QString,    7,
    QMetaType::Void, QMetaType::Int,    9,

 // slots: parameters
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void,

       0        // eod
};

void ReadSmartCard::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ReadSmartCard *_t = static_cast<ReadSmartCard *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->cacheSmartID((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 1: _t->siginitLoadDll(); break;
        case 2: _t->initPort(); break;
        case 3: _t->cacheTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 4: _t->connectDevice((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 5: _t->initLoadLib(); break;
        case 6: _t->FindEhuoyanDevice(); break;
        case 7: _t->SearchSmartCard(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ReadSmartCard::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadSmartCard::cacheSmartID)) {
                *result = 0;
            }
        }
        {
            typedef void (ReadSmartCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadSmartCard::siginitLoadDll)) {
                *result = 1;
            }
        }
        {
            typedef void (ReadSmartCard::*_t)();
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadSmartCard::initPort)) {
                *result = 2;
            }
        }
        {
            typedef void (ReadSmartCard::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadSmartCard::cacheTip)) {
                *result = 3;
            }
        }
        {
            typedef void (ReadSmartCard::*_t)(int );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadSmartCard::connectDevice)) {
                *result = 4;
            }
        }
    }
}

const QMetaObject ReadSmartCard::staticMetaObject = {
    { &QObject::staticMetaObject, qt_meta_stringdata_ReadSmartCard.data,
      qt_meta_data_ReadSmartCard,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ReadSmartCard::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ReadSmartCard::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ReadSmartCard.stringdata0))
        return static_cast<void*>(const_cast< ReadSmartCard*>(this));
    return QObject::qt_metacast(_clname);
}

int ReadSmartCard::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QObject::qt_metacall(_c, _id, _a);
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
void ReadSmartCard::cacheSmartID(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void ReadSmartCard::siginitLoadDll()
{
    QMetaObject::activate(this, &staticMetaObject, 1, Q_NULLPTR);
}

// SIGNAL 2
void ReadSmartCard::initPort()
{
    QMetaObject::activate(this, &staticMetaObject, 2, Q_NULLPTR);
}

// SIGNAL 3
void ReadSmartCard::cacheTip(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 3, _a);
}

// SIGNAL 4
void ReadSmartCard::connectDevice(int _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 4, _a);
}
struct qt_meta_stringdata_ReadAgency_t {
    QByteArrayData data[32];
    char stringdata0[414];
};
#define QT_MOC_LITERAL(idx, ofs, len) \
    Q_STATIC_BYTE_ARRAY_DATA_HEADER_INITIALIZER_WITH_OFFSET(len, \
    qptrdiff(offsetof(qt_meta_stringdata_ReadAgency_t, stringdata0) + ofs \
        - idx * sizeof(QByteArrayData)) \
    )
static const qt_meta_stringdata_ReadAgency_t qt_meta_stringdata_ReadAgency = {
    {
QT_MOC_LITERAL(0, 0, 10), // "ReadAgency"
QT_MOC_LITERAL(1, 11, 13), // "cacheIDCardNO"
QT_MOC_LITERAL(2, 25, 0), // ""
QT_MOC_LITERAL(3, 26, 4), // "data"
QT_MOC_LITERAL(4, 31, 14), // "cacheIDCardTip"
QT_MOC_LITERAL(5, 46, 16), // "cacheStudentInfo"
QT_MOC_LITERAL(6, 63, 7), // "stuName"
QT_MOC_LITERAL(7, 71, 5), // "stuNO"
QT_MOC_LITERAL(8, 77, 4), // "idNO"
QT_MOC_LITERAL(9, 82, 8), // "ticketNO"
QT_MOC_LITERAL(10, 91, 18), // "cacheSmartCardInfo"
QT_MOC_LITERAL(11, 110, 4), // "stat"
QT_MOC_LITERAL(12, 115, 17), // "cacheWatchReadTip"
QT_MOC_LITERAL(13, 133, 11), // "sigAddWatch"
QT_MOC_LITERAL(14, 145, 12), // "cacheBindTip"
QT_MOC_LITERAL(15, 158, 18), // "cacheBindTipIDCard"
QT_MOC_LITERAL(16, 177, 21), // "cacheBindTipSmartCard"
QT_MOC_LITERAL(17, 199, 12), // "DealIDcardNO"
QT_MOC_LITERAL(18, 212, 4), // "name"
QT_MOC_LITERAL(19, 217, 22), // "recevieIDcardNORequest"
QT_MOC_LITERAL(20, 240, 11), // "DealSmartID"
QT_MOC_LITERAL(21, 252, 2), // "id"
QT_MOC_LITERAL(22, 255, 25), // "recevieSmartcardNORequest"
QT_MOC_LITERAL(23, 281, 9), // "StartBind"
QT_MOC_LITERAL(24, 291, 11), // "StartUnBind"
QT_MOC_LITERAL(25, 303, 18), // "recevieBindRequest"
QT_MOC_LITERAL(26, 322, 20), // "recevieUnBindRequest"
QT_MOC_LITERAL(27, 343, 14), // "recevieRequest"
QT_MOC_LITERAL(28, 358, 4), // "type"
QT_MOC_LITERAL(29, 363, 17), // "DealIDCardConnect"
QT_MOC_LITERAL(30, 381, 16), // "DealSmartConnect"
QT_MOC_LITERAL(31, 398, 15) // "checkDeviceConn"

    },
    "ReadAgency\0cacheIDCardNO\0\0data\0"
    "cacheIDCardTip\0cacheStudentInfo\0stuName\0"
    "stuNO\0idNO\0ticketNO\0cacheSmartCardInfo\0"
    "stat\0cacheWatchReadTip\0sigAddWatch\0"
    "cacheBindTip\0cacheBindTipIDCard\0"
    "cacheBindTipSmartCard\0DealIDcardNO\0"
    "name\0recevieIDcardNORequest\0DealSmartID\0"
    "id\0recevieSmartcardNORequest\0StartBind\0"
    "StartUnBind\0recevieBindRequest\0"
    "recevieUnBindRequest\0recevieRequest\0"
    "type\0DealIDCardConnect\0DealSmartConnect\0"
    "checkDeviceConn"
};
#undef QT_MOC_LITERAL

static const uint qt_meta_data_ReadAgency[] = {

 // content:
       7,       // revision
       0,       // classname
       0,    0, // classinfo
      21,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       9,       // signalCount

 // signals: name, argc, parameters, tag, flags
       1,    1,  119,    2, 0x06 /* Public */,
       4,    1,  122,    2, 0x06 /* Public */,
       5,    4,  125,    2, 0x06 /* Public */,
      10,    2,  134,    2, 0x06 /* Public */,
      12,    1,  139,    2, 0x06 /* Public */,
      13,    1,  142,    2, 0x06 /* Public */,
      14,    1,  145,    2, 0x06 /* Public */,
      15,    1,  148,    2, 0x06 /* Public */,
      16,    1,  151,    2, 0x06 /* Public */,

 // slots: name, argc, parameters, tag, flags
      17,    2,  154,    2, 0x0a /* Public */,
      19,    2,  159,    2, 0x0a /* Public */,
      20,    1,  164,    2, 0x0a /* Public */,
      22,    2,  167,    2, 0x0a /* Public */,
      23,    0,  172,    2, 0x0a /* Public */,
      24,    0,  173,    2, 0x0a /* Public */,
      25,    2,  174,    2, 0x0a /* Public */,
      26,    2,  179,    2, 0x0a /* Public */,
      27,    3,  184,    2, 0x0a /* Public */,
      29,    1,  191,    2, 0x0a /* Public */,
      30,    1,  194,    2, 0x0a /* Public */,
      31,    0,  197,    2, 0x0a /* Public */,

 // signals: parameters
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void, QMetaType::QString, QMetaType::QString, QMetaType::QString, QMetaType::QString,    6,    7,    8,    9,
    QMetaType::Void, QMetaType::QString, QMetaType::QString,    3,   11,
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void, QMetaType::QString,    3,
    QMetaType::Void, QMetaType::QString,    3,

 // slots: parameters
    QMetaType::Void, QMetaType::QString, QMetaType::QString,    3,   18,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,   11,    3,
    QMetaType::Void, QMetaType::QString,   21,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,   11,    3,
    QMetaType::Void,
    QMetaType::Void,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,   11,    3,
    QMetaType::Void, QMetaType::Int, QMetaType::QString,   11,    3,
    QMetaType::Void, QMetaType::Int, QMetaType::QString, QMetaType::Int,   11,    3,   28,
    QMetaType::Void, QMetaType::Int,   11,
    QMetaType::Void, QMetaType::Int,   11,
    QMetaType::Void,

       0        // eod
};

void ReadAgency::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        ReadAgency *_t = static_cast<ReadAgency *>(_o);
        Q_UNUSED(_t)
        switch (_id) {
        case 0: _t->cacheIDCardNO((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 1: _t->cacheIDCardTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 2: _t->cacheStudentInfo((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2])),(*reinterpret_cast< QString(*)>(_a[3])),(*reinterpret_cast< QString(*)>(_a[4]))); break;
        case 3: _t->cacheSmartCardInfo((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 4: _t->cacheWatchReadTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 5: _t->sigAddWatch((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 6: _t->cacheBindTip((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 7: _t->cacheBindTipIDCard((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 8: _t->cacheBindTipSmartCard((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 9: _t->DealIDcardNO((*reinterpret_cast< QString(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 10: _t->recevieIDcardNORequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 11: _t->DealSmartID((*reinterpret_cast< QString(*)>(_a[1]))); break;
        case 12: _t->recevieSmartcardNORequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 13: _t->StartBind(); break;
        case 14: _t->StartUnBind(); break;
        case 15: _t->recevieBindRequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 16: _t->recevieUnBindRequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2]))); break;
        case 17: _t->recevieRequest((*reinterpret_cast< int(*)>(_a[1])),(*reinterpret_cast< QString(*)>(_a[2])),(*reinterpret_cast< int(*)>(_a[3]))); break;
        case 18: _t->DealIDCardConnect((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 19: _t->DealSmartConnect((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 20: _t->checkDeviceConn(); break;
        default: ;
        }
    } else if (_c == QMetaObject::IndexOfMethod) {
        int *result = reinterpret_cast<int *>(_a[0]);
        void **func = reinterpret_cast<void **>(_a[1]);
        {
            typedef void (ReadAgency::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheIDCardNO)) {
                *result = 0;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheIDCardTip)) {
                *result = 1;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString , QString , QString , QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheStudentInfo)) {
                *result = 2;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString , QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheSmartCardInfo)) {
                *result = 3;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheWatchReadTip)) {
                *result = 4;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::sigAddWatch)) {
                *result = 5;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheBindTip)) {
                *result = 6;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheBindTipIDCard)) {
                *result = 7;
            }
        }
        {
            typedef void (ReadAgency::*_t)(QString );
            if (*reinterpret_cast<_t *>(func) == static_cast<_t>(&ReadAgency::cacheBindTipSmartCard)) {
                *result = 8;
            }
        }
    }
}

const QMetaObject ReadAgency::staticMetaObject = {
    { &QObject::staticMetaObject, qt_meta_stringdata_ReadAgency.data,
      qt_meta_data_ReadAgency,  qt_static_metacall, Q_NULLPTR, Q_NULLPTR}
};


const QMetaObject *ReadAgency::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->dynamicMetaObject() : &staticMetaObject;
}

void *ReadAgency::qt_metacast(const char *_clname)
{
    if (!_clname) return Q_NULLPTR;
    if (!strcmp(_clname, qt_meta_stringdata_ReadAgency.stringdata0))
        return static_cast<void*>(const_cast< ReadAgency*>(this));
    return QObject::qt_metacast(_clname);
}

int ReadAgency::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QObject::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 21)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 21;
    } else if (_c == QMetaObject::RegisterMethodArgumentMetaType) {
        if (_id < 21)
            *reinterpret_cast<int*>(_a[0]) = -1;
        _id -= 21;
    }
    return _id;
}

// SIGNAL 0
void ReadAgency::cacheIDCardNO(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void ReadAgency::cacheIDCardTip(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 1, _a);
}

// SIGNAL 2
void ReadAgency::cacheStudentInfo(QString _t1, QString _t2, QString _t3, QString _t4)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)), const_cast<void*>(reinterpret_cast<const void*>(&_t2)), const_cast<void*>(reinterpret_cast<const void*>(&_t3)), const_cast<void*>(reinterpret_cast<const void*>(&_t4)) };
    QMetaObject::activate(this, &staticMetaObject, 2, _a);
}

// SIGNAL 3
void ReadAgency::cacheSmartCardInfo(QString _t1, QString _t2)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)), const_cast<void*>(reinterpret_cast<const void*>(&_t2)) };
    QMetaObject::activate(this, &staticMetaObject, 3, _a);
}

// SIGNAL 4
void ReadAgency::cacheWatchReadTip(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 4, _a);
}

// SIGNAL 5
void ReadAgency::sigAddWatch(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 5, _a);
}

// SIGNAL 6
void ReadAgency::cacheBindTip(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 6, _a);
}

// SIGNAL 7
void ReadAgency::cacheBindTipIDCard(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 7, _a);
}

// SIGNAL 8
void ReadAgency::cacheBindTipSmartCard(QString _t1)
{
    void *_a[] = { Q_NULLPTR, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 8, _a);
}
QT_END_MOC_NAMESPACE
