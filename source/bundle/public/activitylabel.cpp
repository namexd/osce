#include "activitylabel.h"
#include <QDebug>

ActivityLabel::ActivityLabel(QWidget * parent)
    :QLabel(parent)
{
    setConnection();
    this->setAlignment(Qt::AlignCenter);
    m_mousePress = false;
}

ActivityLabel::~ActivityLabel(void)
{

}

void ActivityLabel::enterEvent(QEvent *)
{
    emit hover();
    update();
}
void ActivityLabel::mousePressEvent(QMouseEvent *)
{
    emit press();
    update();
}

void ActivityLabel::mouseReleaseEvent(QMouseEvent * ev)
{

    //定义鼠标左键点击事件
    if(ev->button() == Qt::LeftButton)
    {
        Q_UNUSED(ev)
        emit clicked();
    }
}

void ActivityLabel::leaveEvent(QEvent *)
{
    emit leave();
    update();
}

void ActivityLabel::mouseMoveEvent(QMouseEvent* )
{
    QRect widgetRect = this->geometry();
    QPoint mousePos = this->mapFromGlobal(QCursor::pos());
    if (widgetRect.contains(mousePos)== false)
    {
        emit move();
    }
}

void ActivityLabel::setPicName(QString pic_name)
{
    this->pic_name = pic_name;
    setPixmapBack(pic_name);
}

void ActivityLabel::changeHover()
{
    if(m_mousePress == false)
    {
        QString pic = pic_name+QString("_hover");
        if(QImage(pic).isNull())
        {
            pic = pic_name;
        }
        setPixmapBack(pic);
        emit Lhover();
    }
}

void ActivityLabel::changeClick()
{
    if(m_mousePress == false)
    {
        QString pic = pic_name+QString("_pressed");
        if(QImage(pic).isNull())
        {
            pic = pic_name+QString("_hover");
            if(QImage(pic).isNull())
            {
                pic = pic_name;
            }
        }
        setPixmapBack(pic);
        emit Lclicked();
    }
}

void ActivityLabel::changeLeave()
{
    if(m_mousePress == false)
    {
        setPixmapBack(pic_name);
        emit Lhover();
    }
}


bool ActivityLabel::setPixmapBack(QString pic)
{
    if(QImage(pic).isNull())
    {
        return false;
    }
    QImage image(pic);
    if(image.isNull())
        return false;
    QImage result = image.scaled(image.size(),Qt::KeepAspectRatio,Qt::SmoothTransformation);
    this->setPixmap(QPixmap::fromImage(result));
    return true;
}

void ActivityLabel::setMousePress(bool isPressed)
{
   if(isPressed == true)
   {
       m_mousePress = true;
       QString pic = pic_name+QString("_pressed");
       if(QImage(pic).isNull())
       {
           pic = pic_name+QString("_hover");
           if(QImage(pic).isNull())
           {
               pic = pic_name;
           }
       }
       QImage image(pic);
       QImage result = image.scaled(image.size(),Qt::KeepAspectRatio,Qt::SmoothTransformation);
       this->setPixmap(QPixmap::fromImage(result));
   }
   else
   {
       m_mousePress = false;
       QImage image(pic_name);
       QImage result = image.scaled(image.size(),Qt::KeepAspectRatio,Qt::SmoothTransformation);
       this->setPixmap(QPixmap::fromImage(result));
   }
}

void ActivityLabel::setConnection()
{
    connect(this,SIGNAL(hover()),this,SLOT(changeHover()));
    connect(this,SIGNAL(press()),this,SLOT(changeClick()));
    connect(this,SIGNAL(leave()),this,SLOT(changeLeave()));
}
