#ifndef PUSHBUTTON_H
#define PUSHBUTTON_H


#include <QPushButton>
#include <QEvent>
#include <QMouseEvent>
#include <QPainter>

class PushButton :public QPushButton
{
    Q_OBJECT
public:
    explicit PushButton(QWidget *parent = 0);
    ~PushButton();
    void setPicName(QString pic_name);
    void setSize(int width,int height);

protected:
    void enterEvent(QEvent *);
    void leaveEvent(QEvent *);
    void mousePressEvent(QMouseEvent *);
    void mouseReleaseEvent(QMouseEvent *);
    void paintEvent(QPaintEvent *);
private:
    enum ButtonStatus{NORMAL,ENTER,PRESS,NOSTATUS};
    ButtonStatus status;
    QString pic_name;

    int btn_width;
    int btn_height;
    bool mouse_press;
    bool zoomFlag;



};





#endif // PUSHBUTTON_H
