import React, { PropsWithChildren, Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
import { Link } from '@inertiajs/react';

interface DropdownProps extends PropsWithChildren {
  trigger: React.ReactNode;
  align?: 'left' | 'right';
  width?: 'w-48' | 'w-56' | 'w-64';
}

interface DropdownLinkProps {
  href: string;
  method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
  as?: 'a' | 'button';
  children: React.ReactNode;
}

const Dropdown = ({ trigger, align = 'right', width = 'w-48', children }: DropdownProps) => {
  const alignmentClasses = align === 'left' ? 'origin-top-left left-0' : 'origin-top-right right-0';

  return (
    <Menu as="div" className="relative">
      <Menu.Button as={Fragment}>{trigger}</Menu.Button>

      <Transition
        as={Fragment}
        enter="transition ease-out duration-200"
        enterFrom="transform opacity-0 scale-95"
        enterTo="transform opacity-100 scale-100"
        leave="transition ease-in duration-75"
        leaveFrom="transform opacity-100 scale-100"
        leaveTo="transform opacity-0 scale-95"
      >
        <Menu.Items
          className={`absolute z-50 mt-2 ${width} rounded-md shadow-lg ${alignmentClasses} bg-white ring-1 ring-black ring-opacity-5 focus:outline-none`}
        >
          <div className="py-1">{children}</div>
        </Menu.Items>
      </Transition>
    </Menu>
  );
};

const DropdownLink = ({ href, method = 'get', as = 'a', children }: DropdownLinkProps) => {
  return (
    <Menu.Item>
      {({ active }) => (
        <Link
          href={href}
          method={method}
          as={as}
          className={`block w-full px-4 py-2 text-left text-sm leading-5 ${
            active ? 'bg-gray-100 text-gray-900' : 'text-gray-700'
          } transition duration-150 ease-in-out`}
        >
          {children}
        </Link>
      )}
    </Menu.Item>
  );
};

const DropdownButton = ({ children, onClick }: { children: React.ReactNode; onClick: () => void }) => {
  return (
    <Menu.Item>
      {({ active }) => (
        <button
          onClick={onClick}
          className={`block w-full px-4 py-2 text-left text-sm leading-5 ${
            active ? 'bg-gray-100 text-gray-900' : 'text-gray-700'
          } transition duration-150 ease-in-out`}
        >
          {children}
        </button>
      )}
    </Menu.Item>
  );
};

Dropdown.Link = DropdownLink;
Dropdown.Button = DropdownButton;

export default Dropdown;
