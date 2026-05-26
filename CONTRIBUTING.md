# How to contribute to Fooino packages

First off, thank you for taking the time to contribute! 🎉  
We truly appreciate every bug report, feature suggestion and pull request.  
This document will help you get started and make the process as smooth as possible for everyone.

---

## Getting started

### 1. Fork and clone the repository
Start by forking the repository to your own GitHub account, then clone it locally:

```bash
git clone https://github.com/fooino/core.git
cd core
```

### 2. Install dependencies
Use Composer to install all required dependencies:

```bash
composer install
```

### 3. Create a new branch
Since you can not push changes to the main, never work directly on the `main` branch. Create a descriptive branch for your changes:

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b fix/issue-description
```

---

### Development workflow
We practice test-driven development (TDD) to keep the package stable and well-tested.
Here’s the recommended flow:

1. Think about the change
Before writing any code, consider how the feature or bug fix should behave.
If you’re unsure, feel free to open an issue to discuss the idea first.

2. Write a failing test
Write one or more tests that describe the expected behaviour.
Run the test suite and confirm that your new test(s) fail:

```bash
./vendor/bin/pest
```

3. Write the implementation code
Now write the minimum amount of code needed to make the tests pass.
Run the suite again – all tests, including the new ones, should now be green.

4. Check code coverage
We aim for at least 80% code coverage.
You can generate a coverage report with:

```bash
./vendor/bin/pest --coverage
```

---

### Testing
We use [Pest PHP]((https://pestphp.com)) for automated testing.
All tests live in the `tests/` directory and should be written in a clear, descriptive style.

### For new features
Start by writing tests that detail the behaviour you want to achieve.
This not only clarifies the goal but also ensures the feature works correctly from the very beginning.

### For bug fixes
The best way to prove a bug is fixed and prevent it from ever coming back is to:

+ Write a failing test that reproduces the bug.
+ Then fix the code until the test passes.

Can’t figure out the fix?
That’s perfectly fine! Submit a pull request containing just the failing test – it’s still a valuable contribution.

### Running the test suite

```bash
./vendor/bin/pest
```
For more options (parallel, coverage, filtering), check the [Pest documentation](https://pestphp.com/docs).


---

### Submitting a pull request
Once your changes are ready:

1. Push your branch to your fork:

```bash
git push origin feature/your-feature-name
```

2. Go to the original repository on GitHub and open a pull request.
Make sure the base branch is `main`.

3. Write a clear description
Explain what your pull request does, why it’s needed, and reference any related issues.
