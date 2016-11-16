#ifndef ACTIVITYLABEL
#define ACTIVITYLABEL

#include <QWidget>
#include <QLabel>
#include <QMouseEvent>

class ActivityLabel : public QLabel
{
    Q_OBJECT
public:
    ActivityLabel(QWidget * parent = 0);
    ~ActivityLabel(void);
    void setPicName(QString pic_name);
public:
    void setMousePress(bool isPressed);
protected:
    void enterEvent(QEvent *);
    void mousePressEvent(QMouseEvent *);
    void mouseReleaseEvent(QMouseEvent * ev);
    void leaveEvent(QEvent *);
    void mouseMoveEvent(QMouseEvent* );

signals:
    void clicked();
    void hover();
    void press();
    void leave();
    void move();

    void Lhover();
    void Lclicked();
    void Lleave();

public slots:
    void changeHover();
    void changeClick();
    void changeLeave();

private:
    QString pic_name;

    bool setPixmapBack(QString pic);
    void setConnection();

    bool m_mouseOver;     //鼠标是否移过
    bool m_mousePress;    //鼠标是否按下
};


#endif // ACTIVITYLABEL

