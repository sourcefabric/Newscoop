#ifndef _CMS_AUTO_PTR
#define _CMS_AUTO_PTR

#include <memory>

using std::auto_ptr;

// exception classes thrown by SafeAutoPtr template instantiations
class InvalidPointer {};

#ifndef __STL_NOTHROW
#define __STL_NOTHROW throw()
#endif

// SafeAutoPtr
// specialisation of auto_ptr template from the standard library
// Throws InvalidPointer exception when trying to use a NULL pointer
template <class _Tp> class SafeAutoPtr : private auto_ptr<_Tp>
{
public:
	explicit SafeAutoPtr(_Tp* __p = 0) __STL_NOTHROW : auto_ptr<_Tp>(__p) {}

	SafeAutoPtr(SafeAutoPtr& __a) __STL_NOTHROW : auto_ptr<_Tp>(__a.release()) {}

	template <class _Tp1> SafeAutoPtr(SafeAutoPtr<_Tp1>& __a) __STL_NOTHROW
		: auto_ptr<_Tp>(__a.release()) {}

	SafeAutoPtr& operator=(SafeAutoPtr& __a) __STL_NOTHROW
	{ auto_ptr<_Tp>::operator = (__a); return *this; }

	template <class _Tp1>
	SafeAutoPtr& operator=(SafeAutoPtr<_Tp1>& __a) __STL_NOTHROW
	{
		if (__a.get() != this->get())
			reset(__a.release());
		return *this;
	}

	~SafeAutoPtr() __STL_NOTHROW { }

	_Tp& operator*() const throw(InvalidPointer)
	{
		CheckPointer();
		return auto_ptr<_Tp>::operator*();
	}

	_Tp* operator->() const throw(InvalidPointer)
	{
		CheckPointer();
		return auto_ptr<_Tp>::operator->();
	}

	_Tp* get() const __STL_NOTHROW
	{ return auto_ptr<_Tp>::get(); }

	_Tp* release() __STL_NOTHROW
	{ return auto_ptr<_Tp>::release(); }

	void reset(_Tp* __p = 0) __STL_NOTHROW
	{ auto_ptr<_Tp>::reset(__p); }

private:
	void CheckPointer() const throw(InvalidPointer)
	{
		if (get() == NULL)
			throw InvalidPointer();
	}
};

#endif
