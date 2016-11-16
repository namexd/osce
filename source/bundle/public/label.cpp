#include "label.h"


Label::Label(QWidget * parent)
    :QLabel(parent)
{

}

Label::~Label(void)
{

}

void Label::enterEvent(QEvent *)
{
    emit hover();
    update();
}
void Label::mousePressEvent(QMouseEvent *)
{
    emit press();
    update();
}

void Label::mouseReleaseEvent(QMouseEvent * ev)
{

    //定义鼠标左键点击事件
    if(ev->button() == Qt::LeftButton)
    {
        Q_UNUSED(ev)
        emit clicked();
    }
}

void Label::leaveEvent(QEvent *)
{
    emit leave();
    update();
}
