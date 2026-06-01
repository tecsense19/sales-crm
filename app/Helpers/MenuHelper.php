<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        $user = auth()->user();
        $role = $user ? $user->role : 'employee';

        $items = [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'path' => '/',
            ],
            // [
            //     'icon' => 'tables',
            //     'name' => 'Clients',
            //     'path' => '/clients',
            // ],
            [
                'icon' => 'kanban',
                'name' => 'Client Board',
                'path' => '/kanban',
            ],
            [
                'icon' => 'email',
                'name' => 'Templates',
                'path' => '/templates',
            ],
            [
                'name' => 'Import Data',
                'icon' => 'task',
                'path' => '/import',
            ],
        ];

        // Admin only items
        if ($role === 'admin') {
            $items[] = [
                'name' => 'Billing',
                'icon' => 'ecommerce',
                'path' => '/billing',
            ];
            // $items[] = [
            //     'name' => 'SMTP Settings',
            //     'icon' => 'forms',
            //     'path' => '/smtp-settings',
            // ];
            $items[] = [
                'name' => 'SMTP Providers',
                'icon' => 'smtp-provider',
                'path' => '/smtp-providers',
            ];
            $items[] = [
                'name' => 'Campaigns',
                'icon' => 'email',
                'path' => '/campaigns',
            ];
            $items[] = [
                'name' => 'Reports',
                'icon' => 'reports',
                'subItems' => [
                    ['name' => 'General Reports', 'path' => '/reports'],
                    ['name' => 'Team Wise Report', 'path' => '/reports/team-wise'],
                ],
            ];
        }

        return $items;
    }

    public static function getOthersItems()
    {
        return [
            [
                'icon' => 'user-profile',
                'name' => 'User Profile',
                'path' => '/profile',
            ],
        ];
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'CRM SYSTEM',
                'items' => self::getMainNavItems()
            ],
            [
                'title' => 'USER SETTINGS',
                'items' => self::getOthersItems()
            ]
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/')) || request()->is(ltrim($path, '/') . '/*');
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/></svg>',
            'ecommerce' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" fill="currentColor"/></svg>',
            'user-profile' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/></svg>',
            'task' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="currentColor"/></svg>',
            'forms' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>',
            'tables' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z" fill="currentColor"/></svg>',
            'kanban' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="5" height="18" rx="1" fill="currentColor"/><rect x="10" y="3" width="5" height="10" rx="1" fill="currentColor"/><rect x="10" y="15" width="5" height="6" rx="1" fill="currentColor"/><rect x="17" y="3" width="5" height="6" rx="1" fill="currentColor"/><rect x="17" y="11" width="5" height="10" rx="1" fill="currentColor"/></svg>',
            'email' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/></svg>',
            'reports'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" fill="currentColor"/></svg>',
            'smtp-provider' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l5 2.18V11c0 3.5-2.33 6.79-5 7.93-2.67-1.14-5-4.43-5-7.93V7.18L12 5z" fill="currentColor"/></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}
