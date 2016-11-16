#include "pushButton.h"

PushButton::PushButton(QWidget *parent)
    :QPushButton(parent)
{
    status = NORMAL;
    mouse_press = false;
    zoomFlag = false;
    btn_width = 0;
    btn_height = 0;
}

PushButton::~PushButton()
{

}
void PushButton::setSize(int width,int height)
{
    if(width > 0||height >0)
    {
        zoomFlag = true;
        btn_width = width;
        btn_height = height;
    }
}

void PushButton::setPicName(QString pic_name)
{
    this->pic_name = pic_name;

    if(zoomFlag)
    {
        QImage img;
        img.load(pic_name);

        QImage result = img.scaled(btn_width, btn_height,Qt::KeepAspectRatio);
        QPixmap pixmapToShow = QPixmap::fromImage(result);
        setFixedSize(pixmapToShow.size());
    }

    else
    {
        setFixedSize(QPixmap(pic_name).size());
    }
}

void PushButton::enterEvent(QEvent *)
{
    status = ENTER;
    update();
}

void PushButton::mousePressEvent(QMouseEvent *event)
{
    //若点击鼠标左键
    if(event->button() == Qt::LeftButton)
    {
        mouse_press = true;
        status = PRESS;
        update();
    }
}

void PushButton::mouseReleaseEvent(QMouseEvent *event)
{
    //若点击鼠标左键
    if(mouse_press  && this->rect().contains(event->pos()))
    {
        mouse_press = false;
        status = ENTER;
        update();
        emit clicked();
    }
}

void PushButton::leaveEvent(QEvent *)
{
    status = NORMAL;
    update();
}

void PushButton::paintEvent(QPaintEvent *)
{
    QPainter painter(this);
    QPixmap pixmap;
    switch(status)
    {
    case NORMAL:
        {
            pixmap.load(pic_name);
            break;
        }
    case ENTER:
        {
            pixmap.load(pic_name + QString("_pressed"));
            break;
        }
    case PRESS:
        {
            pixmap.load(pic_name + QString("_pressed"));
            break;
        }
    case NOSTATUS:
        {
            pixmap.load(pic_name);
            break;
        }
    default:
        pixmap.load(pic_name);
    }

    painter.drawPixmap(rect(), pixmap);
}

