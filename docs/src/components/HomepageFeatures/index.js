import clsx from 'clsx';
import Heading from '@theme/Heading';
import styles from './styles.module.css';
import Link from "@docusaurus/core/lib/client/exports/Link";

const FeatureList = [
  {
    title: 'Easy to Use',
    //Svg: require('@site/static/img/undraw_docusaurus_mountain.svg').default,
    description: (
      <>
        Docusaurus was designed from the ground up to be easily installed and
        used to get your website up and running quickly.
      </>
    ),
  },
];

function Feature({Svg, title, description}) {
  return (
    <div className={clsx('col col--4')}>
      <div className="text--center">
        <Svg className={styles.featureSvg} role="img" />
      </div>
        <div className="text--center padding-horiz--md">
            <Heading as="h3">{title}</Heading>
            <p>{description}</p>
        </div>
    </div>
  );
}

export default function HomepageFeatures() {
  return (
    <section className={styles.features}>
      <div className="container">
        <div className={clsx('row')}>
            <div className={clsx('col col--8')}>
                <Heading as='h1'>Introduction</Heading>
                <p>LmcRbacMvc is a companion component that extends the functionality of <Link href="https://lm-commons.github.io/LmcRbac">LmcRbac</Link> to provide Role-based Access Control (RBAC) for Laminas MVC applications.</p>
            </div>
        </div>
      </div>
    </section>
  );
}
