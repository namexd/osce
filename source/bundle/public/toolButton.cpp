#include "toolButton.h"

ToolButton::ToolButton(QString picName, QWidget *parent)
    :QToolButton(parent)
{
    m_picName = picName;
    //设置文本颜色
    QPalette textPalette = palette();
    textPalette.setColor(QPalette::ButtonText,QColor(230,230,230));
    setPalette(textPalette);

    //设置文本粗体
    QFont &textFont = const_cast<QFont &>(font());
    textFont.setWeight(QFont::Bold);

    setToolButtonStyle(Qt::ToolButtonTextUnderIcon);

    //设置图标
    QPixmap pixmap(m_picName);
    setIcon(pixmap);
    setIconSize(pixmap.size());

    //设置大小
    setFixedSize(pixmap.width()+25, pixmap.height());
    setAutoRaise(true);
    setObjectName("transparentToolButton");

    m_mouseOver = false;
    m_mousePress = false;
}

ToolButton::~ToolButton()
{

}

void ToolButton::enterEvent(QEvent *)
{
    m_mouseOver = true;
}

void ToolButton::leaveEvent(QEvent *)
{
    m_mouseOver = false;
}

void ToolButton::mousePressEvent(QMouseEvent *event)
{
    if(event->button() == Qt::LeftButton)
    {
        emit clicked();
    }
}

void ToolButton::setMousePress(bool isPressed)
{
    this->m_mousePress = isPressed;
    update();
}

void ToolButton::paintEvent(QPaintEvent *event)
{
    QPixmap pixmap;

    if(m_mousePress)
        pixmap.load(m_picName + QString("_pressed"));
    else if(m_mouseOver)
        pixmap.load(m_picName + QString("_hover"));
    else
        pixmap.load(m_picName);

    setIcon(pixmap);
    setIconSize(pixmap.size());


    QToolButton::paintEvent(event);
}


