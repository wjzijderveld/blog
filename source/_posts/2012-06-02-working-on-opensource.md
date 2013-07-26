---
title: Working on an OpenSource project
layout: post
---
At the company I work at, we have a custom e-commerce
platform that’s in a need of replacement. The codebase is more then 6 years
old and by now, there are more exceptions that rules. The lack of actual MVC
implementation, an ORM or even a DBAL, made maintenance… not very pleasant.

Although we can’t just replace the platform, we did start looking at other
options to create a stable base for future development. For new features that
included a large bit of backend, we already created a new Symfony2 application.
That helped, but didn’t solve the spagetti frontend code. For some new frontend
features, we created a REST service in the Symfony2 application, and used
that in the old frontend application.

But to be able to create a new solid base for a new frontend we started looking
out for options. A completely custom Symfony2 application would simply cost to
much for the customer, a almost ready package as Magento is also an option, but
we weren’t really excited by the news of Ebay buying Magento.

We also looked at some Symfony2 Open Source projects, like Sylius and Vespolina.
Both projects weren’t very far when I started looking. But because Vespolina
started more as a Framework, I started to follow that project more closely.
After a few conversations on IRC with inspiran and iampersistent, I decided
to try and contribute to the project. At that point I had no idea if we ever
would be able to use Vespolina at our company, but I liked to project and just
wanted to help with an Open Source project.

I started with the Vespolina PartnerBundle. A bundle that would be responsible for creating and maintaining parterns in the application. A partner could be a lot more than a customer. It could also be a supplier, a company or a shop employee. The idea is, that the PartnerBundle could even be used in a CRM application.

We now are a few months further, and Vespolina is going steady. Unfortunately, I have little time to spare, so I can’t work on Vespolina as much as I like. At the company we haven’t made a decision about the platform, so I can’t spend time on Vespolina there.

I hope to create some time in the coming weeks. Really need to get going on the forms.