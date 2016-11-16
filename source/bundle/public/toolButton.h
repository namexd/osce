#ifndef TOOLBUTTON_H
#define TOOLBUTTON_H
#include <QToolButton>
#include <QMouseEvent>
#include <QPainter>

class ToolButton : public QToolButton
{
public:
    explicit ToolButton(QString pic_name, QWidget *parent = 0);
    ~ToolButton();
    void setMousePress(bool mouse_press);

protected:
    void enterEvent(QEvent *);
    void leaveEvent(QEvent *);
    void mousePressEvent(QMouseEvent *event);
    void paintEvent(QPaintEvent *event);
public:
    bool m_mouseOver;     //鼠标是否移过
    bool m_mousePress;    //鼠标是否按下
    QString m_picName;

};



#endif // TOOLBUTTON_H
