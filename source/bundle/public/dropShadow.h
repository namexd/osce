#ifndef DROPSHADOW_H
#define DROPSHADOW_H

#include <QDialog>
#include <QWidget>
#include <QPainter>
#include <QMouseEvent>
#include <qmath.h>

class DropShadowWidget : public QDialog
{
    Q_OBJECT

public:
    explicit DropShadowWidget(QWidget *parent = 0);
    ~DropShadowWidget();

protected:
    //virtual void paintEvent(QPaintEvent *event);

private:
    QPoint move_point; //
    bool mouse_press; //按下鼠标左键

};

#endif // DROPSHADOW_H
