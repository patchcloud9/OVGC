# User Management

Admin accounts for the Okanogan Valley Golf Club website are created and managed
entirely by existing admins — there is no public sign-up. This section covers
how to create new accounts, edit existing ones, change passwords, and understand
the two access levels the site supports.

---

## Table of Contents

1. [Concepts](#concepts)
2. [Logging In](#logging-in)
3. [Viewing All Users](#viewing-all-users)
4. [Creating a New User](#creating-a-new-user)
5. [Editing a User](#editing-a-user)
6. [Changing a Password (Admin)](#changing-a-password-admin)
7. [Resetting Your Own Password (Forgot Password)](#resetting-your-own-password-forgot-password)
8. [Deleting a User](#deleting-a-user)
9. [Field Reference](#field-reference)
10. [Tips & Gotchas](#tips--gotchas)

---

## Concepts

### What is a "user"?

A **user** is anyone who has an account on this website. Right now the site has
two kinds of accounts:

| Role | What they can do |
|------|-----------------|
| **Admin** | Log in to the admin panel and manage all site content — events, pages, menus, users, etc. |
| **User** | Has an account but cannot access the admin panel. Currently this role is reserved for possible future use. |

Most accounts you will create will be **Admin** accounts (for staff or board
members who need to update the site).

### How are accounts created?

People **cannot sign themselves up** — that feature is turned off. Every account
must be created manually by someone who is already an admin. Think of it like
issuing a key card: only someone who already has access can create a new one.

### What happens to passwords?

Passwords are never stored as plain text. The site converts each password into a
scrambled code (called a **hash**) that cannot be reversed. When someone logs in,
the site checks the scrambled code — not the original password — so even the
people who run the server cannot see anyone's password.

---

## Logging In

Navigate to:

```
https://www.okanoganvalleygolf.com/login
```

<!-- SCREENSHOT: The login page — show the full page with the email field, password field, "Forgot your password?" link, and the Login button. Capture it on a desktop browser at normal zoom. -->

Enter your **email address** and **password**, then click **Log In**.

> [!note]
> If you type your password wrong five times in five minutes, the site will
> temporarily lock the login form for that browser. Wait a few minutes and
> try again, or use the Forgot Password link to reset your password.

After a successful login, admins are taken directly to the admin dashboard.

---

## Viewing All Users

From the admin dashboard, click **Users** in the navigation, or go directly to:

```
https://www.okanoganvalleygolf.com/admin/users
```

<!-- SCREENSHOT: The Users list page — show the full grid of user cards with at least two users visible. Each card should show the user's initial avatar, name, email, role badge, and the Edit/Delete buttons. Also capture the search box and Role filter dropdown at the top. -->

The page shows a card for each account. Each card displays:

- The user's **initials** (as a coloured circle)
- Their **name** and **email address**
- A **role badge** (Admin or User)
- Their **account ID number**
- **Edit** and **Delete** buttons

### Searching and filtering

Use the **search box** to find a user by name or email. Use the **Role** dropdown
to show only Admins or only Users. The list filters as you type — no need to
press Enter.

---

## Creating a New User

1. Go to **Admin → Users** (`/admin/users`).
2. Click the **Add User** button (top right of the page).

<!-- SCREENSHOT: The "Add User" button on the Users list page — highlight or circle the button so it is easy to spot. -->

3. Fill in the form:

   - **Name** — The person's full name (e.g., `Jane Smith`).
   - **Email** — Their email address. This must be unique; no two accounts can
     share the same email.
   - **Password** — Choose a strong password (at least 8 characters). You will
     need to share this with the person securely — the site does not send a
     welcome email automatically.
   - **Role** — Choose **Admin** if they need to manage the website, or **User**
     for a non-admin account.

<!-- SCREENSHOT: The Create User form — show all four fields (Name, Email, Password, Role dropdown) filled in with example data before clicking Save. The Role dropdown should be open showing the Admin/User options. -->

4. Click **Create User**.

If everything is correct, you will see a green success message and be returned
to the user list. If there is a problem (for example, the email is already in
use), a red error message will appear and the form will stay open so you can fix
it.

> [!tip]
> After creating an account, tell the new admin to change their password right
> away. They can do this through the Forgot Password link on the login page
> (see [Resetting Your Own Password](#resetting-your-own-password-forgot-password))
> or you can change it for them through the Edit screen.

---

## Editing a User

1. Go to **Admin → Users**.
2. Find the user's card and click **Edit**.

<!-- SCREENSHOT: The Edit User form — show the form pre-filled with a user's existing name, email, and role. The password field should be visibly empty (with placeholder text). Also show the "Danger Zone" delete section at the bottom of the page. -->

3. Update any fields you need to change:

   - **Name** — Change the display name.
   - **Email** — Change the login email address.
   - **Password** — Leave this **blank** if you do not want to change the
     password. Only fill it in if you are setting a new password.
   - **Role** — Promote to Admin or demote to User.

4. Click **Save Changes**.

> [!note]
> The password field on the Edit screen is always empty. This is normal — the
> site never shows the current password. You only need to type something here
> if you are changing it.

---

## Changing a Password (Admin)

An admin can change any user's password through the Edit screen:

1. Go to **Admin → Users** and click **Edit** on the account.
2. Leave the **Name**, **Email**, and **Role** fields as they are.
3. Type the new password (at least 8 characters) in the **Password** field.
4. Click **Save Changes**.

The old password is immediately replaced. The user will need to use the new
password the next time they log in.

> [!warning]
> There is no "current password" confirmation when an admin changes a password
> through this screen. Make sure you are editing the correct account before
> saving.

---

## Resetting Your Own Password (Forgot Password)

If you have forgotten your password and cannot log in:

1. Go to the login page: `https://www.okanoganvalleygolf.com/login`
2. Click **Forgot your password?**

<!-- SCREENSHOT: The login page with the "Forgot your password?" link visible — circle or highlight the link. -->

3. Enter your **email address** and click **Send Reset Link**.

<!-- SCREENSHOT: The Forgot Password page — show the single email input field and the Send Reset Link button. -->

4. Check your email inbox for a message with a reset link. The link is valid for
   **one hour**.

> [!note]
> The site always shows the same message ("If that email address is registered,
> you will receive a password reset link shortly") whether or not the email
> matched an account. This is a privacy measure — it prevents someone from
> using this page to find out which emails have accounts.

5. Click the link in the email. You will be taken to a page where you can enter
   a new password.

<!-- SCREENSHOT: The Reset Password page — show the two fields (New Password and Confirm New Password) and the Reset Password button. -->

6. Type your **new password** (at least 8 characters) in both fields and click
   **Reset Password**.
7. You will be redirected to the login page. Log in with your new password.

> [!warning]
> The reset link expires after **one hour**. If you click an expired link, the
> site will show an error. Go back to the Forgot Password page and request a
> fresh link.

---

## Deleting a User

> [!warning]
> Deleting a user account is permanent and cannot be undone. The account and all
> its data are removed immediately.

1. Go to **Admin → Users**.
2. Find the user's card and click **Delete**, or open their **Edit** screen and
   scroll to the **Danger Zone** section at the bottom.
3. Confirm the deletion in the pop-up dialog.

<!-- SCREENSHOT: The delete confirmation modal/dialog — show the dialog asking the admin to confirm they want to delete the account, with the Cancel and Delete buttons visible. -->

> [!tip]
> If someone leaves the organization and you are not sure whether to delete their
> account, consider changing their password to something unknown instead of
> deleting. That way their account history is preserved and you can restore
> access later if needed.

---

## Field Reference

| Field | Required? | Rules | Notes |
|-------|-----------|-------|-------|
| **Name** | Yes | 2–100 characters | Used as the display name throughout the admin panel. |
| **Email** | Yes | Must be a valid email; must be unique across all accounts | This is the username for login. |
| **Password** | Yes (create) / No (edit) | At least 8 characters | On the Edit screen, leave blank to keep the current password. |
| **Role** | Yes | Admin or User | Admins can access the admin panel; Users cannot. |

---

## Tips & Gotchas

> [!tip]
> **You can only create accounts — the site does not send a welcome email.**
> After creating an account you will need to share the login credentials with
> the new user yourself (for example, by phone or a secure message). Remind
> them to change their password immediately using the Forgot Password flow.

> [!warning]
> **You cannot lock yourself out, but you can create a situation where no one
> can log in.** If you delete or demote all admin accounts, no one will be able
> to access the admin panel. Always keep at least one active admin account.

> [!note]
> **The "User" role has no admin access.** If someone needs to update the
> website, their role must be set to **Admin**. A role of "User" is effectively
> a placeholder for possible future features.

> [!warning]
> **Rate limiting applies to account creation.** You can create up to 3 new
> accounts within a 5-minute window. If you need to add many accounts at once,
> pause briefly between each one.

> [!note]
> **Changing a user's email changes their login username.** After updating the
> email on the Edit screen, the user must log in with the new email address.
> Make sure to let them know.

> [!tip]
> **Use the search and filter tools on the Users list** when you have many
> accounts. Type a name or email in the search box, or use the Role dropdown
> to show only Admins, to quickly find the account you need.

> [!note]
> **Password reset links expire in one hour.** If a user contacts you saying
> their reset link did not work, ask them to request a new one from the Forgot
> Password page. Each new request cancels any previous link.
