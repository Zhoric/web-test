<?php

/**
 * Перечисление ролей, присутствующих в системе.
 * Значение констант соответствует значениям поля slug в таблице ролей.
 */
abstract class UserRole
{
    const Admin = "admin";
    const Lecturer = "lecturer";
    const Student = "student";
}
